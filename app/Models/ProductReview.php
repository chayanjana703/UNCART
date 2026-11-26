<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'rating',
        'title',
        'comment',
        'images',
        'helpful_votes',
        'unhelpful_votes',
        'vendor_reply',
        'vendor_replied_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'helpful_votes' => 'integer',
            'unhelpful_votes' => 'integer',
            'images' => 'array',
            'vendor_replied_at' => 'datetime',
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

    public function votes()
    {
        return $this->hasMany(ReviewVote::class, 'review_id');
    }

    /**
     * Get the vote type submitted by current authenticated user ('helpful', 'unhelpful', or null).
     */
    public function getUserVoteAttribute(): ?string
    {
        if (!auth()->check()) {
            return null;
        }

        if ($this->relationLoaded('votes')) {
            $vote = $this->votes->firstWhere('user_id', auth()->id());
            return $vote ? $vote->vote_type : null;
        }

        return ReviewVote::where('review_id', $this->id)
            ->where('user_id', auth()->id())
            ->value('vote_type');
    }

    /**
     * Resolve image URL to accessible public URL.
     */
    public static function resolveImageUrl(?string $url): string
    {
        if (empty($url)) {
            return '';
        }

        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://') || str_starts_with($url, 'data:')) {
            return $url;
        }

        if (str_starts_with($url, '/storage/') || str_starts_with($url, 'storage/')) {
            return asset(ltrim($url, '/'));
        }

        return asset('storage/' . ltrim($url, '/'));
    }

    /**
     * Get array of fully resolved image URLs.
     */
    public function getImageUrlsAttribute(): array
    {
        $imgs = $this->images ?? [];
        if (!is_array($imgs)) {
            $imgs = is_string($imgs) ? json_decode($imgs, true) : [];
        }
        if (!is_array($imgs)) {
            return [];
        }

        return array_values(array_filter(array_map([static::class, 'resolveImageUrl'], $imgs)));
    }

    /**
     * Alias for photos attribute used across Admin and Vendor views.
     */
    public function getPhotosAttribute(): array
    {
        return $this->image_urls;
    }

    /**
     * Check if review is from a verified buyer who completed an order for this product.
     */
    public function getIsVerifiedPurchaseAttribute(): bool
    {
        if (!$this->user_id || !$this->product_id) {
            return false;
        }

        return \App\Models\OrderItem::where('product_id', $this->product_id)
            ->whereHas('order', function ($query) {
                $query->where('user_id', $this->user_id)
                    ->whereIn('payment_status', ['paid', 'completed']);
            })
            ->exists();
    }
}
