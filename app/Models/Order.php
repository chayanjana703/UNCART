<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'subtotal',
        'discount_amount',
        'shipping_amount',
        'tax_amount',
        'total_amount',
        'coupon_code',
        'shipping_method',
        'shipping_address',
        'billing_address',
        'payment_method',
        'payment_status',
        'transaction_id',
        'order_status',
        'tracking_number',
        'carrier_name',
        'notes',
        'cancel_reason',
        'return_reason',
        'return_status',
        'delivered_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'shipping_amount' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'shipping_address' => 'array',
            'billing_address' => 'array',
            'delivered_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->order_status, ['placed', 'confirmed', 'processing']);
    }

    public function canBeReturned(): bool
    {
        return $this->order_status === 'delivered' && ($this->return_status === 'none' || empty($this->return_status));
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->order_status) {
            'placed' => ['bg' => 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300', 'label' => 'Order Placed'],
            'confirmed' => ['bg' => 'bg-sky-100 text-sky-800 dark:bg-sky-950/60 dark:text-sky-300', 'label' => 'Confirmed'],
            'processing' => ['bg' => 'bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300', 'label' => 'Processing'],
            'packed' => ['bg' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-950/60 dark:text-indigo-300', 'label' => 'Packed'],
            'shipped' => ['bg' => 'bg-purple-100 text-purple-800 dark:bg-purple-950/60 dark:text-purple-300', 'label' => 'Shipped'],
            'out_for_delivery' => ['bg' => 'bg-cyan-100 text-cyan-800 dark:bg-cyan-950/60 dark:text-cyan-300', 'label' => 'Out for Delivery'],
            'delivered' => ['bg' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300', 'label' => 'Delivered'],
            'cancelled' => ['bg' => 'bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300', 'label' => 'Cancelled'],
            'returned' => ['bg' => 'bg-orange-100 text-orange-800 dark:bg-orange-950/60 dark:text-orange-300', 'label' => 'Returned'],
            default => ['bg' => 'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-300', 'label' => ucfirst($this->order_status)],
        };
    }

    public function getTrackingTimelineAttribute(): array
    {
        $stages = [
            'placed' => ['title' => 'Order Placed', 'desc' => 'Order has been received by UNCART'],
            'confirmed' => ['title' => 'Payment & Order Confirmed', 'desc' => 'Seller verified inventory and payment verified'],
            'packed' => ['title' => 'Packed by Vendor', 'desc' => 'Package has been inspected, boxed and labeled'],
            'shipped' => ['title' => 'Shipped with Carrier', 'desc' => 'In transit with ' . ($this->carrier_name ?: 'Express Courier')],
            'out_for_delivery' => ['title' => 'Out for Delivery', 'desc' => 'Courier agent has left the distribution hub for your address'],
            'delivered' => ['title' => 'Delivered', 'desc' => 'Delivered to recipient with verified signature'],
        ];

        $keys = array_keys($stages);
        $currentIndex = array_search($this->order_status, $keys);
        if ($currentIndex === false) {
            $currentIndex = ($this->order_status === 'cancelled' || $this->order_status === 'returned') ? -1 : 0;
        }

        $timeline = [];
        foreach ($keys as $idx => $key) {
            $isCompleted = ($idx <= $currentIndex && $currentIndex >= 0);
            $isCurrent = ($idx === $currentIndex);
            $timeline[] = [
                'key' => $key,
                'title' => $stages[$key]['title'],
                'desc' => $stages[$key]['desc'],
                'completed' => $isCompleted,
                'current' => $isCurrent,
            ];
        }

        return $timeline;
    }
}
