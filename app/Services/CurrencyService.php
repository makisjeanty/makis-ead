<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CurrencyService
{
    protected $baseCurrency = 'USD';

    /**
     * Convert amount from one currency to another
     */
    public function convert(float $amount, string $from, string $to): float
    {
        if ($from === $to) {
            return $amount;
        }

        $rates = $this->getExchangeRates();

        // Convert to base currency (USD) first
        $amountInBase = $amount / $rates[$from];

        // Then convert to target currency
        return round($amountInBase * $rates[$to], 2);
    }

    /**
     * Get exchange rates (cached)
     */
    public function getExchangeRates(): array
    {
        return Cache::remember('exchange_rates', config('currency.api.cache_duration'), function () {
            // Try to fetch from API first
            $apiRates = $this->fetchRatesFromAPI();
            
            if ($apiRates) {
                return $apiRates;
            }

            // Fallback to config rates
            return config('currency.exchange_rates');
        });
    }

    /**
     * Fetch rates from external API
     */
    protected function fetchRatesFromAPI(): ?array
    {
        $apiKey = config('currency.api.key');
        
        if (!$apiKey) {
            return null;
        }

        try {
            $response = Http::get("https://v6.exchangerate-api.com/v6/{$apiKey}/latest/USD");

            if ($response->successful()) {
                $data = $response->json();
                return $data['conversion_rates'] ?? null;
            }
        } catch (\Exception $e) {
            \Log::warning('Currency API fetch failed', ['error' => $e->getMessage()]);
        }

        return null;
    }

    /**
     * Format amount with currency symbol
     */
    public function format(float $amount, string $currency): string
    {
        $currencyData = config("currency.currencies.{$currency}");
        
        if (!$currencyData) {
            return number_format($amount, 2);
        }

        $symbol = $currencyData['symbol'];
        $decimals = $currencyData['decimals'];
        $formatted = number_format($amount, $decimals, ',', '.');

        return "{$symbol} {$formatted}";
    }

    /**
     * Get user's preferred currency
     */
    public function getUserCurrency(): string
    {
        if (auth()->check() && auth()->user()->preferred_currency) {
            return auth()->user()->preferred_currency;
        }

        return session('currency', config('currency.default'));
    }

    /**
     * Set user's preferred currency
     */
    public function setUserCurrency(string $currency): void
    {
        session(['currency' => $currency]);

        if (auth()->check()) {
            auth()->user()->update(['preferred_currency' => $currency]);
        }
    }

    /**
     * Get all supported currencies
     */
    public function getSupportedCurrencies(): array
    {
        return config('currency.currencies');
    }
}
