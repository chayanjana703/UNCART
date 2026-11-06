<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'type',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'postal_code',
        'country',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }

    public function getFullNameAttribute(): string
    {
        return $this->name ?? '';
    }

    public function setFullNameAttribute($value): void
    {
        $this->attributes['name'] = $value;
    }

    public function getFormattedAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address_line1,
            $this->address_line2,
            $this->city . ($this->state ? ', ' . $this->state : '') . ($this->postal_code ? ' ' . $this->postal_code : ''),
            $this->country,
        ]);
        return implode(' • ', $parts);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
