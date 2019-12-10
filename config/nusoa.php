<?php

return [
    'directorySearch' => [
        'baseUrl' => env('DIRECTORY_SEARCH_URL', 'https://northwestern-prod.apigee.net/directory-search'),
        'apiKey' => env('DIRECTORY_SEARCH_API_KEY'),
    ],

    'sso' => [
        'enableOpenAm11' => env('USE_NEW_WEBSSO_SERVER', false),
        'realm' => env('WEBSSO_REALM', 'northwestern'),
        'authTree' => env('WEBSSO_TREE', env('DUO_ENABLED', false) == true ? 'ldap-and-duo' : 'ldap-registry'),
        'cookieName' => env('WEBSSO_COOKIE_NAME', 'nusso'),
        'apiProperties' => [
            'netid' => env('WEBSSO_API_NETID_PROPERTY', 'username'),
            'mfa' => env('WEBSSO_API_MFA_PROPERTY', 'isDuoAuthenticated'),
        ],

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
