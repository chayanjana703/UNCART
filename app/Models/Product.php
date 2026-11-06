<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'category_id',
        'name',
        'slug',
        'short_description',
        'description',
        'price',
        'sale_price',
        'stock_quantity',
        'sku',
        'specifications',
        'images',
        'is_active',
        'is_featured',
        'status',
        'rating_avg',
        'rating_count',
        'views_count',
        'sales_count',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'rating_avg' => 'decimal:2',
            'stock_quantity' => 'integer',
            'rating_count' => 'integer',
            'views_count' => 'integer',
            'sales_count' => 'integer',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'specifications' => 'array',
            'images' => 'array',
        ];
    }

    public function getCurrentPriceAttribute(): float
    {
        if ($this->sale_price && $this->sale_price > 0 && $this->sale_price < $this->price) {
            return (float) $this->sale_price;
        }
        return (float) $this->price;
    }

    public function getDiscountPercentageAttribute(): int
    {
        if ($this->sale_price && $this->sale_price > 0 && $this->sale_price < $this->price) {
            return (int) round((($this->price - $this->sale_price) / $this->price) * 100);
        }
        return 0;
    }

    public static function resolveImageUrl(?string $url): string
    {
        if (empty($url)) {
            return 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=800&auto=format&fit=crop&q=80';
        }

        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://') || str_starts_with($url, 'data:')) {
            return $url;
        }

        if (str_starts_with($url, '/storage/') || str_starts_with($url, 'storage/')) {
            return asset(ltrim($url, '/'));
        }

        return asset('storage/' . ltrim($url, '/'));
    }

    public function getFeaturedImageAttribute(): string
    {
        if (is_array($this->images) && count($this->images) > 0) {
            return static::resolveImageUrl($this->images[0]);
        }
        return 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=800&auto=format&fit=crop&q=80';
    }

    public function getPrimaryImageAttribute(): string
    {
        return $this->featured_image;
    }

    public function getGalleryImagesAttribute(): array
    {
        if (is_array($this->images) && count($this->images) > 0) {
            return array_values(array_map([static::class, 'resolveImageUrl'], $this->images));
        }
        return [$this->featured_image];
    }

    public function getImagesWithUrlsAttribute(): array
    {
        if (!is_array($this->images) || count($this->images) === 0) {
            return [];
        }

        $list = [];
        foreach ($this->images as $img) {
            if (!empty($img)) {
                $list[] = [
                    'raw' => $img,
                    'url' => static::resolveImageUrl($img),
                ];
            }
        }
        return $list;
    }

    public function getInStockAttribute(): bool
    {
        return $this->stock_quantity > 0;
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class)->where('status', 'approved')->latest();
    }

    public function allReviews()
    {
        return $this->hasMany(ProductReview::class)->latest();
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
