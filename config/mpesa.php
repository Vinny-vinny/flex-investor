<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Account
    |--------------------------------------------------------------------------
    |
    | This is the default account to be used when none is specified.
    */

    'default' => 'production',

    /*
    |--------------------------------------------------------------------------
    | Native File Cache Location
    |--------------------------------------------------------------------------
    |
    | When using the Native Cache driver, this will be the relative directory
    | where the cache information will be stored.
    */

    'cache_location' => '../cache',

    /*
    |--------------------------------------------------------------------------
    | Accounts
    |--------------------------------------------------------------------------
    |
    | These are the accounts that can be used with the package. You can configure
    | as many as needed. Two have been setup for you.
    |
    | Sandbox: Determines whether to use the sandbox, Possible values: sandbox | production
    | Initiator: This is the username used to authenticate the transaction request
    | LNMO:
    |    paybill: Your paybill number
    |    shortcode: Your business shortcode
    |    passkey: The passkey for the paybill number
    |    callback: Endpoint that will be be queried on completion or failure of the transaction.
    |
    /*
         * Passkey , requested from mpesa
         */

    /*return $this->register(config('mpesa.c2b.short_code'))
            ->onConfirmation(config('mpesa.c2b.confirmation_url'))
            ->onValidation(config('mpesa.c2b.validation_url'))
            ->submit();
  |--------------------------------------------------------------------------
  | B2C array
  |--------------------------------------------------------------------------
  |
  | If you are sending payments to customers or b2b
  |
  */
    'b2c' => [
        /*
         * Sending app consumer key
         */
        'consumer_key' => 'pWT3xatmco6WP3KjVFQrIzvlYr2zg7JB',
        /*
         * Sending app consumer secret
         */
        'consumer_secret' => 'tYHWwlNzu9mK0OKy',
        /*
         * Shortcode sending funds
         */
        'short_code' => 894173,
        /*
        * This is the user initiating the transaction, usually from the Mpesa organization portal
        * Make sure this was the user who was used to 'GO LIVE'
        * https://org.ke.m-pesa.com/
        */
        'initiator' => 'MosesDev',
        /*
         * The user security credential.
         * Go to https://developer.safaricom.co.ke/test_credentials and paste your initiator password to generate
         * security credential
         */
        'security_credential' => env('MPESA_SECURITY_KEY'),
        /*
         * Notification URL for timeout
         */
        'timeout_url' => env('APP_URL') . 'api/b2c/callback/timeout/',
        /**
         * Result URL
         */
        'result_url' => env('APP_URL') . 'api/b2c/callback/result/',
    ],

    'c2b' => [
        'short_code' => 894173,
        'confirmation_url' => env('APP_URL') . 'api/v1/callbacks/confirmation/',
        /**
         * Result URL
         */
        'validation_url' => env('APP_URL') . 'api/v1/callbacks/validation/',
    ],

    'b2c_express' => [
        /*
         * Sending app consumer key
         */
        'consumer_key' => 'SgaEPPOq4KNsN0VeKyonpdWAbcGAtojYhFfCxn1Cv5vbek7m',
        /*
         * Sending app consumer secret
         */
        'consumer_secret' => 'sJjT4Btr4gcKYGn9HMbE5wdhTXUrE1Csk1pyE9bosgwMs2lXE3iGg3XGQA43rXXS',
        /*
         * Shortcode sending funds
         */
        'short_code' => 3030295,
        /*
        * This is the user initiating the transaction, usually from the Mpesa organization portal
        * Make sure this was the user who was used to 'GO LIVE'
        * https://org.ke.m-pesa.com/
        */
        'initiator' => 'Wakanyi',
        /*
         * The user security credential.
         * Go to https://developer.safaricom.co.ke/test_credentials and paste your initiator password to generate
         * security credential
         */
        'security_credential' => env('B2C_EXPRESS_MPESA_SECURITY_KEY', 'b2c_express_security'),
        /*
         * Notification URL for timeout
         */
        'timeout_url' => env('APP_URL') . 'api/b2c/callback/timeout/',
        /**
         * Result URL
         */
        'result_url' => env('APP_URL') . 'api/b2c/callback/result/',
        /**
         * Query URL
         */
        'query_url' => env('APP_URL') . 'api/mpesa/query/callback',
        /**
         * Query URL TimeOut
         */
        'query_timeout_url' => env('APP_URL') . 'api/mpesa/query/timeout/callback',
        ],
    'accounts' => [
        'production' => [
            'sandbox' => false,
            'key' => 'Qvd384UG8gbsI6J9zRkLId67mce9sdtw',
            'secret' => '5BvsGcM70g92bDCz',
            'initiator' => 'Wakanyi',
            'id_validation_callback' => 'http://example.com/callback?secret=RmxleFNha28xMjMyPT0=',
            'lnmo' => [
                'paybill' => 4101015,
                'shortcode' => 4101015,
                'passkey' => '2264a2ddac092bfbc72a7e0314d3b7d06ccda46127639885b4634e0fe0fb4cc8',
                'callback' => env('APP_URL') . 'api/v1/callbacks/stk?secret=RmxleFNha28xMjMyPT0=',
            ]
        ],
    ],
];
