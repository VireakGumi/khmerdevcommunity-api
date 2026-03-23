<?php

return [
    'emails' => array_values(array_filter(array_map(
        static fn ($value) => strtolower(trim($value)),
        explode(',', (string) env('COMMUNITY_ADMIN_EMAILS', 'chanvireak906@gmail.com,roeunvireak0@gmail.com'))
    ))),
];
