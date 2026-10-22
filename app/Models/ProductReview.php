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
}
