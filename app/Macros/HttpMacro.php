<?php

namespace App\Macros;

use Illuminate\Support\Facades\Http;

class HttpMacro
{
    public function paystack()
    {
        return function () {
            return Http::withToken(config('app.paystack_secret_key'))
                ->withHeaders([
                    'content-type' => 'application/json',
                ])
                ->baseUrl(config('app.paystack_url'));
        };
    }

    public function termii()
    {
        return function () {
            return Http::withHeaders([
                'content-type' => 'application/json',
            ])
                ->baseUrl(config('app.termii_base_url'));
        };
    }

    public function flutterwave()
    {
        return function () {
            return Http::withToken(config('app.flutterwave_secret_key'))
                ->withHeaders([
                    'content-type' => 'application/json',
                ])
                ->baseUrl(config('app.flutterwave_url'));
        };
    }

}
