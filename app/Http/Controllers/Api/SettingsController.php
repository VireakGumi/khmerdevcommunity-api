<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        return response()->json([
            'notification_preferences' => $request->user()->notification_preferences ?? [
                'mentions' => true,
                'comments' => true,
                'follows' => true,
                'messages' => true,
                'events' => true,
            ],
            'privacy_settings' => $request->user()->privacy_settings ?? [
                'show_email' => false,
                'show_location' => true,
                'allow_messages' => true,
            ],
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
            'privacy_settings' => ['required', 'array'],
            'privacy_settings.show_email' => ['required', 'boolean'],
            'privacy_settings.show_location' => ['required', 'boolean'],
            'privacy_settings.allow_messages' => ['required', 'boolean'],
        ]);

        $request->user()->forceFill($data)->save();

        return response()->json($request->user()->fresh(['posts']));
    }
}
