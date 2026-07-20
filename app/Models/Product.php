<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'currency',
        'stock',
        'image',
        'status',
        'category_id',
    ];

    protected $appends = ['currency_sign', 'converted_price', 'average_rating'];

    function getCurrencySignAttribute()
    {
        return match($this->currency) {
            'USD' => '$',
            'EUR' => '€',
            'CAD' => 'C$',
            'INR' => '₹',
            'AUD' => 'A$',
            'AED' => 'د.إ',
            'GBP' => '£',

            default => '$',
        };
    }

    /**
     * Get the converted price based on authenticated user's currency
     */
    function getConvertedPriceAttribute()
    {
        try {
            $user = Auth::user();
            
            // If no authenticated user or same currency, return original price
            if (!$user || $user->currency === $this->currency) {
                return (float) $this->price;
            }

            // Convert using CurrencyRate model
            return \App\Models\CurrencyRate::convert(
                $this->price, 
                $this->currency, 
                $user->currency
            );
        } catch (\Exception $e) {
            // Fallback to original price if conversion fails
            return (float) $this->price;
        }
    }

    function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    function wishlist()
    {
        return $this->hasOne(Wishlist::class)->where('user_id', Auth::id());
    }

    function productRatings()
    {
        return $this->hasMany(ProductRating::class);
    }

    function getAverageRatingAttribute()
    {
        $ratings = $this->productRatings;
        if ($ratings->isEmpty()) {
            return 0;
        }
        return round($ratings->avg('rating'), 1);
    }
}
