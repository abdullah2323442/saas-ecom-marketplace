<?php

return [
    'central_domains' => array_values(array_filter(array_map(
        static fn (string $domain): string => trim($domain),
        explode(',', (string) env('CENTRAL_DOMAINS', 'localhost,127.0.0.1'))
    ))),
];
