<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Wishlist;
use App\Models\Product;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlistItems = Wishlist::where('user_id', Auth::id())
            ->with(['product.category', 'product.vendor'])
            ->latest()
            ->paginate(12);

        return view('pages.wishlist', compact('wishlistItems'));
    }

    public function toggle(Request $request)
    {
        $productId = $request->input('product_id');
        $product = Product::findOrFail($productId);

        $exists = Wishlist::where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->first();

        if ($exists) {
            $exists->delete();
            $status = 'removed';
            $message = "{$product->name} removed from your wishlist.";
        } else {
            Wishlist::create([
                'user_id' => Auth::id(),
                'product_id' => $productId,
            ]);
            $status = 'added';
            $message = "{$product->name} added to your wishlist!";
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'status' => $status,
                'message' => $message,
                'wishlist_count' => Wishlist::where('user_id', Auth::id())->count(),
            ]);
        }

        return back()->with('success', $message);
    }
}
