<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'size_id',
        'color_id',
        'name',
        'price',
        'stock',
        'sku',
    ];

    protected $appends = ['converted_price'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function size()
    {
        return $this->belongsTo(Size::class);
    }

    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    /**
     * Get the converted price of this variant (inherits product base price if null)
     */
    public function getConvertedPriceAttribute()
    {
        try {
            $user = Auth::user();
            $product = $this->product;
            if (!$product) {
                return 0.0;
            }

            $basePrice = $this->price !== null ? (float) $this->price : (float) $product->price;
            $baseCurrency = $product->currency;

            // If no authenticated user or same currency, return original price
            if (!$user || $user->currency === $baseCurrency) {
                return $basePrice;
            }

            // Convert using CurrencyRate model
            return \App\Models\CurrencyRate::convert(
                $basePrice, 
                $baseCurrency, 
                $user->currency
            );
        } catch (\Exception $e) {
            return $this->price !== null ? (float) $this->price : 0.0;
        }
    }
}
