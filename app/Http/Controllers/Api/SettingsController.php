<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    private function defaultNotificationPreferences(): array
    {
        return [
            'mentions' => true,
            'comments' => true,
            'follows' => true,
            'messages' => true,
            'events' => true,
            'jobs' => true,
            'product_updates' => false,
            'donation_updates' => true,
        ];
    }

    private function defaultPrivacySettings(): array
    {
        return [
            'show_email' => false,
            'show_location' => true,
            'allow_messages' => true,
            'show_followers' => true,
            'show_activity' => true,
            'profile_visibility' => 'public',
        ];
    }

    public function show(Request $request): JsonResponse
    {
        return response()->json([
            'notification_preferences' => array_replace($this->defaultNotificationPreferences(), $request->user()->notification_preferences ?? []),
            'privacy_settings' => array_replace($this->defaultPrivacySettings(), $request->user()->privacy_settings ?? []),
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $data = $request->validate([
            'notification_preferences' => ['required', 'array'],
            'notification_preferences.mentions' => ['required', 'boolean'],
            'notification_preferences.comments' => ['required', 'boolean'],
            'notification_preferences.follows' => ['required', 'boolean'],
            'notification_preferences.messages' => ['required', 'boolean'],
            'notification_preferences.events' => ['required', 'boolean'],
            'notification_preferences.jobs' => ['required', 'boolean'],
            'notification_preferences.product_updates' => ['required', 'boolean'],
            'notification_preferences.donation_updates' => ['required', 'boolean'],
            'privacy_settings' => ['required', 'array'],
            'privacy_settings.show_email' => ['required', 'boolean'],
            'privacy_settings.show_location' => ['required', 'boolean'],
            'privacy_settings.allow_messages' => ['required', 'boolean'],
            'privacy_settings.show_followers' => ['required', 'boolean'],
            'privacy_settings.show_activity' => ['required', 'boolean'],
            'privacy_settings.profile_visibility' => ['required', 'string', 'in:public,community,private'],
        ]);

        $request->user()->forceFill($data)->save();

        return response()->json($request->user()->fresh(['posts']));
    }
}
