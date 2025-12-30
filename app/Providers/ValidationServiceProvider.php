<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Blade;

class ValidationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Custom validation rule for currency format
        Validator::extend('currency_format', function ($attribute, $value, $parameters, $validator) {
            // Accepts values like: 10.99, 100, 1000.00
            return preg_match('/^\d+(\.\d{1,2})?$/', $value);
        }, 'The :attribute must be a valid currency format.');

        // Custom validation rule for positive numeric values
        Validator::extend('positive_numeric', function ($attribute, $value, $parameters, $validator) {
            return is_numeric($value) && $value > 0;
        }, 'The :attribute must be a positive number.');

        // Custom validation rule for valid URL or base64 image
        Validator::extend('url_or_base64', function ($attribute, $value, $parameters, $validator) {
            // Check if it's a valid URL
            if (filter_var($value, FILTER_VALIDATE_URL)) {
                return true;
            }
            
            // Check if it's a base64 encoded image
            if (preg_match('/^data:image\/(\w+);base64,/', $value, $matches)) {
                $imageType = $matches[1];
                $data = substr($value, strpos($value, ',') + 1);
                
                // Check if data is valid base64
                if (base64_decode($data, true) !== false) {
                    return in_array($imageType, ['jpeg', 'jpg', 'png', 'gif', 'webp']);
                }
            }
            
            return false;
        }, 'The :attribute must be a valid URL or base64 encoded image.');

        // Register custom Blade directives
        Blade::if('currency', function ($currency) {
            return session('currency', config('currency.default')) === $currency;
        });
    }
}