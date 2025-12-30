<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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

        if (!isset($rates[$from]) || !isset($rates[$to])) {
            Log::warning('Currency not supported for conversion', [
                'from' => $from,
                'to' => $to,
                'available_rates' => array_keys($rates)
            ]);
            throw new \InvalidArgumentException("Currency not supported: {$from} or {$to}");
        }

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
        return Cache::remember('exchange_rates', config('currency.api.cache_duration', 3600), function () {
            // Try to fetch from API first
            $apiRates = $this->fetchRatesFromAPI();
            
            if ($apiRates) {
                return $apiRates;
            }

            Log::warning('Using fallback currency rates from config');
            // Fallback to config rates
            return config('currency.exchange_rates', []);
        });
    }

    /**
     * Fetch rates from external API
     */
    protected function fetchRatesFromAPI(): ?array
    {
        $apiKey = config('currency.api.key');
        
        if (!$apiKey) {
            Log::info('No API key configured for currency rates, using fallback');
            return null;
        }

        try {
            $response = Http::timeout(10)->get("https://v6.exchangerate-api.com/v6/{$apiKey}/latest/USD");

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['result']) && $data['result'] === 'success') {
                    Log::info('Successfully fetched currency rates from API');
                    return $data['conversion_rates'] ?? null;
                } else {
                    Log::warning('Currency API returned error', ['error' => $data['result'] ?? 'Unknown error']);
                    return null;
                }
            } else {
                Log::warning('Currency API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return null;
            }
        } catch (\Exception $e) {
            Log::error('Currency API fetch failed with exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Format amount with currency symbol
     */
    public function format(float $amount, string $currency): string
    {
        $currencyData = config("currency.currencies.{$currency}");
        
        if (!$currencyData) {
            Log::warning('Currency format configuration not found', ['currency' => $currency]);
            return number_format($amount, 2, '.', ',');
        }

        $symbol = $currencyData['symbol'];
        $decimals = $currencyData['decimals'] ?? 2;
        $position = $currencyData['symbol_position'] ?? 'before';
        
        $formatted = number_format($amount, $decimals, $currencyData['decimal_separator'] ?? '.', $currencyData['thousands_separator'] ?? ',');

        if ($position === 'before') {
            return "{$symbol}{$formatted}";
        } else {
            return "{$formatted} {$symbol}";
        }
    }

    /**
     * Get user's preferred currency
     */
    public function getUserCurrency(): string
    {
        if (auth()->check() && auth()->user()->preferred_currency) {
            return auth()->user()->preferred_currency;
        }

        return session('currency', config('currency.default', 'USD'));
    }

    /**
     * Set user's preferred currency
     */
    public function setUserCurrency(string $currency): void
    {
        // Validate currency
        $supportedCurrencies = $this->getSupportedCurrencies();
        if (!isset($supportedCurrencies[$currency])) {
            throw new \InvalidArgumentException("Currency {$currency} is not supported");
        }

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
        return config('currency.currencies', []);
    }
    
    /**
     * Get the symbol for a currency
     */
    public function getSymbol(string $currency): string
    {
        $currencyData = config("currency.currencies.{$currency}");
        return $currencyData['symbol'] ?? $currency;
    }
}