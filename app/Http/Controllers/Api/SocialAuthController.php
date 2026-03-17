<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Symfony\Component\HttpFoundation\Response;

class SocialAuthController extends Controller
{
    private const ALLOWED_PROVIDERS = ['github', 'google'];

    public function redirect(Request $request, string $provider): Response
    {
        abort_unless(in_array($provider, self::ALLOWED_PROVIDERS, true), 404);

        $request->session()->put('oauth_redirect', $this->sanitizeRedirect($request->query('redirect')));

        return Socialite::driver($provider)
            ->scopes($this->providerScopes($provider))
            ->redirect();
    }

    public function callback(string $provider): RedirectResponse
    {
        abort_unless(in_array($provider, self::ALLOWED_PROVIDERS, true), 404);
        $redirect = $this->sanitizeRedirect(session()->pull('oauth_redirect'));

        try {
            /** @var SocialiteUser $socialUser */
            $socialUser = Socialite::driver($provider)->stateless()->user();
            $user = $this->resolveUser($provider, $socialUser);
            $token = $user->createToken("{$provider}-oauth", ['feed:read', 'projects:read', 'messages:read']);

            return redirect()->away($this->buildFrontendCallbackUrl($token->accessToken, null, $redirect));
        } catch (\Throwable $exception) {
            report($exception);

            return redirect()->away($this->buildFrontendCallbackUrl(
                token: null,
                error: 'Unable to finish social sign in. Please try again.',
                redirect: $redirect,
            ));
        }
    }

    private function resolveUser(string $provider, SocialiteUser $socialUser): User
    {
        $email = $socialUser->getEmail();

        $user = User::query()
            ->where('auth_provider', $provider)
            ->where('auth_provider_id', (string) $socialUser->getId())
            ->first();

        if (! $user && $email) {
            $user = User::query()->where('email', $email)->first();
        }

        if (! $user) {
            $user = new User([
                'name' => $socialUser->getName() ?: $socialUser->getNickname() ?: ucfirst($provider).' Member',
                'username' => $this->generateUsername($socialUser),
                'email' => $email ?: $this->fallbackEmail($provider, $socialUser),
                'password' => Hash::make(Str::password(32)),
                'headline' => ucfirst($provider).' member',
                'location' => 'Cambodia',
                'bio' => 'Joined with '.$provider.' and ready to build in the Khmer developer community.',
                'skills' => [ucfirst($provider)],
                'availability' => 'Open to collaboration',
                'portfolio_headline' => 'Building with the Khmer developer community',
                'portfolio_summary' => 'A new builder profile ready to grow into a public portfolio.',
                'social_links' => [
                    'github' => $provider === 'github' ? $this->providerProfileUrl($provider, $socialUser) : null,
                    'linkedin' => null,
                    'portfolio' => null,
                    'x' => null,
                ],
                'featured_work' => [],
                'profile_palette' => [
                    'primary' => '#5865f2',
                    'secondary' => '#3b82f6',
                    'surface' => '#0f172a',
                ],
            ]);
        }

        $meta = $this->providerMeta($provider, $socialUser);

        $user->forceFill([
            'auth_provider' => $provider,
            'auth_provider_id' => (string) $socialUser->getId(),
            'auth_provider_meta' => $meta,
            'name' => $user->name ?: ($socialUser->getName() ?: $socialUser->getNickname() ?: $user->name),
            'avatar_url' => $socialUser->getAvatar() ?: $user->avatar_url,
        ]);

        if ($email && ! $user->email) {
            $user->email = $email;
        }

        if ($email && ! $user->email_verified_at) {
            $user->email_verified_at = Carbon::now();
        }

        $socialLinks = $user->social_links ?: [
            'github' => null,
            'linkedin' => null,
            'portfolio' => null,
            'x' => null,
        ];

        if ($provider === 'github' && empty($socialLinks['github'])) {
            $socialLinks['github'] = $this->providerProfileUrl($provider, $socialUser);
        }

        $user->social_links = $socialLinks;
        $user->save();

        return $user->refresh();
    }

    private function buildFrontendCallbackUrl(?string $token, ?string $error = null, ?string $redirect = null): string
    {
        $frontend = rtrim(config('services.frontend.url'), '/');
        $query = [];

        if ($token) {
            $query['token'] = $token;
        }

        if ($error) {
            $query['error'] = $error;
        }

        if ($redirect) {
            $query['redirect'] = $redirect;
        }

        return $frontend.'/#/auth/callback'.($query ? '?'.http_build_query($query) : '');
    }

    private function providerScopes(string $provider): array
    {
        return match ($provider) {
            'github' => ['read:user', 'user:email'],
            'google' => ['openid', 'profile', 'email'],
            default => [],
        };
    }

    private function providerMeta(string $provider, SocialiteUser $socialUser): array
    {
        $raw = is_array($socialUser->user) ? $socialUser->user : [];

        return array_filter([
            'provider' => $provider,
            'nickname' => $socialUser->getNickname(),
            'avatar' => $socialUser->getAvatar(),
            'profile_url' => $this->providerProfileUrl($provider, $socialUser),
            'bio' => $raw['bio'] ?? null,
        ], fn ($value) => $value !== null && $value !== '');
    }

    private function providerProfileUrl(string $provider, SocialiteUser $socialUser): ?string
    {
        $raw = is_array($socialUser->user) ? $socialUser->user : [];

        return match ($provider) {
            'github' => $raw['html_url'] ?? ($socialUser->getNickname() ? "https://github.com/{$socialUser->getNickname()}" : null),
            'google' => null,
            default => null,
        };
    }

    private function generateUsername(SocialiteUser $socialUser): string
    {
        $base = $socialUser->getNickname()
            ?: Str::before((string) $socialUser->getEmail(), '@')
            ?: $socialUser->getName()
            ?: 'builder';

        $username = Str::of($base)
            ->lower()
            ->replaceMatches('/[^a-z0-9]+/', '')
            ->value();

        if ($username === '') {
            $username = 'builder';
        }

        $candidate = $username;
        $suffix = 1;

        while (User::query()->where('username', $candidate)->exists()) {
            $candidate = $username.$suffix;
            $suffix++;
        }

        return $candidate;
    }

    private function fallbackEmail(string $provider, SocialiteUser $socialUser): string
    {
        return sprintf('%s+%s@oauth.khdev.community', $provider, $socialUser->getId());
    }

    private function sanitizeRedirect(?string $redirect): string
    {
        if (! is_string($redirect) || $redirect === '' || ! str_starts_with($redirect, '/') || str_starts_with($redirect, '//')) {
            return '/feed';
        }

        return $redirect;
    }
}
