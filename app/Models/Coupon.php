<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'discount_type',
        'discount_value',
        'min_spend',
        'max_discount',
        'usage_limit',
        'used_count',
        'expires_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'discount_value' => 'decimal:2',
            'min_spend' => 'decimal:2',
            'max_discount' => 'decimal:2',
            'usage_limit' => 'integer',
            'used_count' => 'integer',
            'expires_at' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function isValidForSubtotal(float $subtotal): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->expires_at && Carbon::parse($this->expires_at)->isPast()) {
            return false;
        }

        if ($this->usage_limit > 0 && $this->used_count >= $this->usage_limit) {
            return false;
        }

        if ($subtotal < (float) $this->min_spend) {
            return false;
        }

        return true;
    }

    public function calculateDiscount(float $subtotal): float
    {
        if ($this->discount_type === 'percent') {
            $discount = ($subtotal * (float) $this->discount_value) / 100;
            if ($this->max_discount && $discount > (float) $this->max_discount) {
                return (float) $this->max_discount;
            }
            return round($discount, 2);
        }

        return min($subtotal, (float) $this->discount_value);
    }
}
