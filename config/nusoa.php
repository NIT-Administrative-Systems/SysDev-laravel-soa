<?php


return [
    'directorySearch' => [
        'baseUrl' => env('DIRECTORY_SEARCH_URL', 'https://northwestern-prod.apigee.net/directory-search'),
        'apiKey' => env('DIRECTORY_SEARCH_API_KEY'),
    ],

    'sso' => [
        'openAmBaseUrl' => env('WEBSSO_URL_BASE', 'https://websso.it.northwestern.edu'),
    ],

    'eventHub' => [
        'baseUrl' => env('EVENT_HUB_BASE_URL'),
        'apiKey' => env('EVENT_HUB_API_KEY'),
        'hmacVerificationSharedSecret' => env('EVENT_HUB_HMAC_VERIFICATION_SHARED_SECRET'),
        'hmacVerificationHeader' => env('EVENT_HUB_HMAC_VERIFICATION_HEADER', 'X-HMAC-Signature'),

        // HMAC algorithm we'll register the webhook with -- this must correspond to a type in the EventHub API docs
        'hmacVerificationAlgorithmForRegistration' => env('EVENT_HUB_HMAC_VERIFICATION_ALGORITHM_TYPE_REGISTRATION', 'HmacSHA256'),

         // Matching PHP algorithm type, passed to `hash_hmac()`. You can run `hash_algos()` to see what you have available.
        'hmacVerificationAlgorithmForPHPHashHmac' => env('EVENT_HUB_HMAC_VERIFICATION_ALGORITHM_TYPE_PHP', 'sha256'),
    ],
];
