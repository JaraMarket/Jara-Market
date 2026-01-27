<?php

namespace App\Providers;

use Illuminate\Support\Facades\Config;
use App\Models\Setting;
use Schema;
use App\Enums\Sms\SmsGatewayEnum;
use App\Services\SmsGateways\Termii;
use App\Contracts\SmsGatewayInterface;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use App\Contracts\PaymentGatewayInterface;
use App\Services\PaymentGateways\Paystack;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(PaymentGatewayInterface::class, Paystack::class);
        $this->app->singleton(SmsGatewayInterface::class, function ($app) {
            $smsGateway = config('app.sms_gateway');
            if ($smsGateway === SmsGatewayEnum::TERMII()) {
                return new Termii();
            }
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        Password::defaults(function () {
            return Password::min(8)->letters()->mixedCase()->numbers()->symbols()->uncompromised();
        });

        try {
            if (Schema::hasTable('settings')) {
                $dbTimezone = Setting::where('key', 'timezone')->value('value');
            }
        } catch (\Exception $e) {
            // Prevent crash if DB is unreachable (common in CI/ECS startup)
            \Illuminate\Support\Facades\Log::error('AppServiceProvider: DB connection failed - ' . $e->getMessage());
        }
    
        // Fallback to config/app.php if DB value is missing
        $timezone = $dbTimezone ?? config('app.timezone');
    
        // Apply globally
        Config::set('app.timezone', $timezone);
        date_default_timezone_set($timezone);
    }
}
