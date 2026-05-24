<?php

return [
    'api_key' => env('FLOW_API_KEY'),
    'secret_key' => env('FLOW_SECRET_KEY'),
    'url' => env('FLOW_URL', 'https://sandbox.flow.cl/api'),
    'return_url' => env('APP_URL') . '/pago/retorno',
    'confirmation_url' => env('APP_URL') . '/pago/confirmar',
    'ssl_verify' => env('FLOW_SSL_VERIFY', true),
];
