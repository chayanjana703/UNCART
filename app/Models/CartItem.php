<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'product_id',
        'quantity',
        'options',
        'is_saved_for_later',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'options' => 'array',
            'is_saved_for_later' => 'boolean',
        ];
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getSubtotalAttribute(): float
    {
        return $this->quantity * ($this->product ? $this->product->current_price : 0);
    }
}
