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

    public static function resolveMediaUrl(?string $url, string $default): string
    {
        if (empty($url)) {
            return $default;
        }
        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://') || str_starts_with($url, 'data:')) {
            return $url;
        }
        if (str_starts_with($url, '/storage/') || str_starts_with($url, 'storage/')) {
            return asset(ltrim($url, '/'));
        }
        return asset('storage/' . ltrim($url, '/'));
    }

    public function getShopLogoUrlAttribute(): string
    {
        return static::resolveMediaUrl($this->shop_logo, 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=400&auto=format&fit=crop&q=80');
    }

    public function getShopBannerUrlAttribute(): string
    {
        return static::resolveMediaUrl($this->shop_banner, 'https://images.unsplash.com/photo-1550745165-9bc0b252726f?w=1400&auto=format&fit=crop&q=80');
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
