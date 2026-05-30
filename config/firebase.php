<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Firebase Credentials
    |--------------------------------------------------------------------------
    |
    | Specify the path to your Firebase service account JSON file.
    | This is used to authenticate with Firebase services.
    |
    */

    'credentials' => [
        'auto' => env('FIREBASE_CREDENTIALS'),
        'json' => [
            'project_id' => env('FIREBASE_PROJECT_ID'),
            'private_key' => env('FIREBASE_PRIVATE_KEY'),
            'client_email' => env('FIREBASE_CLIENT_EMAIL'),
            'token_uri' => env('FIREBASE_TOKEN_URI', 'https://oauth2.googleapis.com/token'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Firebase Project Configuration
    |--------------------------------------------------------------------------
    |
    | Set the default Firebase project ID.
    |
    */

    'project_id' => env('FIREBASE_PROJECT_ID'),

    /*
    |--------------------------------------------------------------------------
    | Firebase Storage Bucket
    |--------------------------------------------------------------------------
    |
    | Set the default Firebase Cloud Storage bucket.
    |
    */

    'storage_bucket' => env('FIREBASE_STORAGE_BUCKET'),

    /*
    |--------------------------------------------------------------------------
    | Firebase Database URL
    |--------------------------------------------------------------------------
    |
    | Set the Firebase Realtime Database URL.
    |
    */

    'database_url' => env('FIREBASE_DATABASE_URL'),

    /*
    |--------------------------------------------------------------------------
    | Firebase Messaging
    |--------------------------------------------------------------------------
    |
    | Configuration for Firebase Cloud Messaging.
    |
    */

    'messaging' => [
        'default' => env('FIREBASE_MESSAGING_DEFAULT', 'default'),

        'projects' => [
            'default' => [
                'credentials' => env('FIREBASE_CREDENTIALS'),
            ],
        ],
    ],

];