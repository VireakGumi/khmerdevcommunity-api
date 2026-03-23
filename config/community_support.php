<?php

return [
    'donation' => [
        'enabled' => (bool) env('COMMUNITY_DONATION_ENABLED', true),
        'currency' => env('COMMUNITY_DONATION_CURRENCY', 'USD'),
        'title' => env('COMMUNITY_DONATION_TITLE', 'Support KhmerDevCommunity'),
        'summary' => env('COMMUNITY_DONATION_SUMMARY', 'Help fund hosting, product development, moderation, and community growth for KhmerDevCommunity.'),
        'khqr_payload' => env('COMMUNITY_DONATION_KHQR', ''),
        'khqr_account_name' => env('COMMUNITY_DONATION_KHQR_ACCOUNT_NAME', 'KhmerDevCommunity'),
        'contact_email' => env('COMMUNITY_DONATION_CONTACT_EMAIL', 'roeunvireak0@gmail.com'),
        'tiers' => [
            ['label' => '$5', 'copy' => 'hosting help'],
            ['label' => '$20', 'copy' => 'tools and moderation'],
            ['label' => '$50', 'copy' => 'features and events'],
        ],
        'buckets' => [
            [
                'title' => 'Hosting and uptime',
                'caption' => 'Keep the website online and fast',
                'badge' => 'Core',
                'copy' => 'Support server costs, storage, delivery, and the maintenance work that keeps KhmerDevCommunity stable.',
                'metrics' => ['server costs', 'uptime'],
                'details' => ['hosting', 'cdn', 'storage'],
            ],
            [
                'title' => 'Product development',
                'caption' => 'Fund new features and polish',
                'badge' => 'Build',
                'copy' => 'Help pay for the work behind feed improvements, messaging polish, portfolio updates, and overall product quality.',
                'metrics' => ['ui work', 'feature work'],
                'details' => ['feed', 'messages', 'portfolio'],
            ],
            [
                'title' => 'Community growth',
                'caption' => 'Back events and outreach',
                'badge' => 'Grow',
                'copy' => 'Contributions also help the platform support discovery, community events, and a stronger network around Khmer builders.',
                'metrics' => ['events', 'reach'],
                'details' => ['meetups', 'jobs', 'discoverability'],
            ],
        ],
    ],
];
