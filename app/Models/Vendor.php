<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'shop_name',
        'shop_slug',
        'shop_description',
        'shop_logo',
        'shop_banner',
        'phone',
        'email',
        'address',
        'kyc_status',
        'kyc_document',
        'bank_name',
        'account_number',
        'ifsc_code',
        'commission_rate',
        'status',
        'return_policy',
        'shipping_policy',
        'total_payouts',
    ];

    protected function casts(): array
    {
        return [
            'commission_rate' => 'decimal:2',
            'total_payouts' => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payouts()
    {
        return $this->hasMany(VendorPayout::class);
    }

    public function inquiries()
    {
        return $this->hasMany(CustomerInquiry::class);
    }
}
