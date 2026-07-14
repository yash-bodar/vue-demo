<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'description',
        'discount_type',
        'discount_value',
        'min_purchase_amount',
        'max_uses',
        'times_used',
        'max_uses_per_user',
        'valid_from',
        'valid_until',
        'is_active',
    ];

    protected $casts = [
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Validate if coupon is applicable
     */
    public function isValid(): bool
    {
        // Check if active
        if (!$this->is_active) {
            return false;
        }

        // Check if max uses reached
        if ($this->max_uses && $this->times_used >= $this->max_uses) {
            return false;
        }

        // Check date validity
        $now = Carbon::now();
        if ($this->valid_from && $now < $this->valid_from) {
            return false;
        }

        if ($this->valid_until && $now > $this->valid_until) {
            return false;
        }

        return true;
    }

    /**
     * Check if user can use this coupon
     */
    public function canUserUse(User $user): bool
    {
        if (!$this->isValid()) {
            return false;
        }

        if ($this->max_uses_per_user) {
            $usedCount = Order::where('user_id', $user->id)
                ->where('coupon_id', $this->id)
                ->count();
            
            if ($usedCount >= $this->max_uses_per_user) {
                return false;
            }
        }

        return true;
    }

    /**
     * Calculate discount amount
     */
    public function calculateDiscount(float $amount): float
    {
        if ($this->discount_type === 'percentage') {
            return ($amount * $this->discount_value) / 100;
        }

        return min($this->discount_value, $amount); // Don't discount more than total
    }

    /**
     * Get relationship to orders
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'coupon_id');
    }
}
