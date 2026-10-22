<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CartItem;
use Symfony\Component\HttpFoundation\Response;

class CartSessionMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $sessionId = $request->session()->getId();

        // If user is logged in, attach any guest cart items from this session to the user
        if (Auth::check()) {
            $userId = Auth::id();

            // Find items belonging to this session without a user_id
            $guestItems = CartItem::where('session_id', $sessionId)
                ->whereNull('user_id')
                ->get();

            foreach ($guestItems as $item) {
                // Check if user already has this product in cart
                $existing = CartItem::where('user_id', $userId)
                    ->where('product_id', $item->product_id)
                    ->where('is_saved_for_later', $item->is_saved_for_later)
                    ->first();

                if ($existing) {
                    $existing->quantity += $item->quantity;
                    $existing->save();
                    $item->delete();
                } else {
                    $item->user_id = $userId;
                    $item->save();
                }
            }
        }

        return $next($request);
    }
}
