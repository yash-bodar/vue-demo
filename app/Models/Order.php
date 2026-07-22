<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'address_id',
        'total_amount',
        'shipping',
        'discount_amount',
        'final_amount',
        'payment_status',
        'payment_intent_id',
        'status',
        'items',
        'currency',
        'coupon_id',
    ];

    protected $casts = [
        'items' => 'array',
    ];

    protected $appends = ['currency_sign', 'product_count', 'converted_price'];

    function getConvertedPriceAttribute()
    {
        try {
            if ($this->currency === 'INR') {
                return (float) $this->total_amount;
            }
            return CurrencyRate::convert($this->total_amount, $this->currency, 'INR');
        } catch (\Exception $e) {
            return (float) $this->total_amount;
        }
    }

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

    function getProductCountAttribute()
    {
        return is_array($this->items) ? count($this->items) : 0;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
