<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ProductReview;
use App\Models\ReviewVote;
use App\Models\Product;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'comment' => 'required|string|min:10',
            'images' => 'nullable|array',
        ]);

        $review = ProductReview::updateOrCreate(
            [
                'product_id' => $validated['product_id'],
                'user_id' => Auth::id(),
            ],
            [
                'rating' => $validated['rating'],
                'title' => $validated['title'] ?? null,
                'comment' => $validated['comment'],
                'images' => $validated['images'] ?? null,
                'status' => 'approved',
            ]
        );

        // Recalculate average rating for product
        $product = Product::find($validated['product_id']);
        $avg = ProductReview::where('product_id', $product->id)->where('status', 'approved')->avg('rating') ?: 0;
        $count = ProductReview::where('product_id', $product->id)->where('status', 'approved')->count();

        $product->update([
            'rating_avg' => round($avg, 2),
            'rating_count' => $count,
        ]);

        return back()->with('success', 'Your review has been submitted. Thank you for your feedback!');
    }

    public function vote(Request $request, $id)
    {
        $validated = $request->validate([
            'vote_type' => 'required|in:helpful,unhelpful',
        ]);

        $review = ProductReview::findOrFail($id);

        $existing = ReviewVote::where('review_id', $review->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existing) {
            if ($existing->vote_type === $validated['vote_type']) {
                $existing->delete();
                if ($validated['vote_type'] === 'helpful') {
                    $review->decrement('helpful_votes');
                } else {
                    $review->decrement('unhelpful_votes');
                }
                $voted = false;
            } else {
                if ($existing->vote_type === 'helpful') {
                    $review->decrement('helpful_votes');
                    $review->increment('unhelpful_votes');
                } else {
                    $review->decrement('unhelpful_votes');
                    $review->increment('helpful_votes');
                }
                $existing->update(['vote_type' => $validated['vote_type']]);
                $voted = true;
            }
        } else {
            ReviewVote::create([
                'review_id' => $review->id,
                'user_id' => Auth::id(),
                'vote_type' => $validated['vote_type'],
            ]);

            if ($validated['vote_type'] === 'helpful') {
                $review->increment('helpful_votes');
            } else {
                $review->increment('unhelpful_votes');
            }
            $voted = true;
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'helpful' => $review->helpful_votes,
                'unhelpful' => $review->unhelpful_votes,
            ]);
        }

        return back()->with('success', 'Thank you for your vote.');
    }

    public function destroy($id)
    {
        $review = ProductReview::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $productId = $review->product_id;
        $review->delete();

        // Recalculate
        $product = Product::find($productId);
        if ($product) {
            $avg = ProductReview::where('product_id', $product->id)->where('status', 'approved')->avg('rating') ?: 0;
            $count = ProductReview::where('product_id', $product->id)->where('status', 'approved')->count();
            $product->update([
                'rating_avg' => round($avg, 2),
                'rating_count' => $count,
            ]);
        }

        return back()->with('info', 'Your review has been removed.');
    }
}
