<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Common HTTP Status Code Messages
    |--------------------------------------------------------------------------
    | Error messages used for both web and API responses.
    */

    '200' => [
        'title' => 'OK',
        'message' => 'The request was successful.',
    ],

    '400' => [
        'title' => 'Bad Request',
        'message' => 'The request was invalid or cannot be served.',
    ],

    '401' => [
        'title' => 'Unauthorized',
        'message' => 'Authentication is required or has failed.',
    ],

    '403' => [
        'title' => 'Forbidden',
        'message' => 'You are not authorized to access this resource.',
    ],

    '404' => [
        'title' => 'Not Found',
        'message' => 'The requested resource was not found.',
    ],

    '405' => [
        'title' => 'Method Not Allowed',
        'message' => 'The HTTP method used is not allowed for this route.',
    ],

    '422' => [
        'title' => 'Unprocessable Entity',
        'message' => 'The provided data is invalid. Please check and try again.',
    ],

    '429' => [
        'title' => 'Too Many Requests',
        'message' => 'You have made too many requests in a short period of time.',
    ],

    '500' => [
        'title' => 'Internal Server Error',
        'message' => 'An unexpected error occurred. Please try again later.',
    ],

    'default' => [
        'title' => 'Error',
        'message' => 'An unknown error occurred.',
    ],
];
