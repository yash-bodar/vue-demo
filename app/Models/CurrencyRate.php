<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CurrencyRate extends Model
{
    protected $fillable = [
        'currency_code',
        'rate_to_inr',
        'updated_at'
    ];

    public $timestamps = false;

    protected $table = 'currency_rates';

    /**
     * Get rate to INR for a currency
     */
    public static function getRateToINR($currencyCode)
    {
        return self::where('currency_code', strtoupper($currencyCode))->value('rate_to_inr') ?? 1.0;
    }

    /**
     * Convert amount from one currency to another using INR as base
     */
    public static function convert($amount, $fromCurrency, $toCurrency)
    {
        if ($fromCurrency === $toCurrency) {
            return round($amount, 2);
        }

        $fromRate = self::getRateToINR($fromCurrency);
        $amountInINR = $amount * $fromRate;

        $toRate = self::getRateToINR($toCurrency);
        $convertedAmount = $amountInINR / $toRate;
        return round($convertedAmount, 2);
    }
}
