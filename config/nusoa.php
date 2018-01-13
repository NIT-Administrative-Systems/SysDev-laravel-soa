<?php

return [
    'directorySearch' => [
        'baseUrl' => env('DIRECTORY_SEARCH_URL', 'https://northwestern-prod.apigee.net/directory-search'),
        'apiKey' => env('DIRECTORY_SEARCH_API_KEY'),
    ],

    'sso' => [
        'openAmBaseUrl' => env('WEBSSO_URL_BASE', 'https://websso.it.northwestern.edu'),
    ],

    'messageQueue' => [
        /*
        * For reference, the URLs are:
        *
        *   - Dev: <https://northwestern-dev.apigee.net>
        *   - QA: <https://northwestern-test.apigee.net>
        *   - Prod: <https://northwestern-prod.apigee.net>
        *
        * But, change this in your `.env` file, NOT here.
        */
        'baseUrl' => env('MQ_API_URL', 'https://northwestern-dev.apigee.net'),
        'apiKey' => env('MQ_API_KEY'),
        'username' => env('MQ_API_USERNAME'),
        'password' => env('MQ_API_PASSWORD'),

        'publishPath' => env('MQ_API_PATH_PUB', 'mq-publisher'),
        'consumePath' => env('MQ_API_PATH_SUB', 'mq-consumer'),
        'maxConsumptionPerRun' => env('MQ_API_SUB_MAX_PER_RUN', 100),
    ],
];
