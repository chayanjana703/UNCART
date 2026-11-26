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
            'images' => 'nullable|array|max:5',
            'images.*' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if ($value instanceof \Illuminate\Http\UploadedFile) {
                        if (!$value->isValid()) {
                            $fail('One of the uploaded images is corrupted or failed to upload.');
                        }
                        $allowedMimes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp', 'image/gif'];
                        if (!in_array($value->getMimeType(), $allowedMimes)) {
                            $fail('Review images must be valid JPEG, PNG, WEBP, or GIF files.');
                        }
                        if ($value->getSize() > 5 * 1024 * 1024) {
                            $fail('Review images must not exceed 5MB each.');
                        }
                    } elseif (!is_string($value)) {
                        $fail('The ' . $attribute . ' must be a valid image file or URL.');
                    }
                },
            ],
            'existing_images' => 'nullable|array',
            'existing_images.*' => 'nullable|string',
        ]);

        // 1. Gather any existing images kept by user
        $imagePaths = [];
        if ($request->has('existing_images') && is_array($request->input('existing_images'))) {
            foreach ($request->input('existing_images') as $img) {
                if (is_string($img) && !empty(trim($img))) {
                    $imagePaths[] = trim($img);
                }
            }
        }

        // 2. Process newly uploaded image files or string URLs
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                if ($file instanceof \Illuminate\Http\UploadedFile && $file->isValid()) {
                    $path = $file->store('reviews', 'public');
                    $imagePaths[] = '/storage/' . $path;
                }
            }
        } elseif ($request->has('images') && is_array($request->input('images'))) {
            foreach ($request->input('images') as $img) {
                if (is_string($img) && !empty(trim($img))) {
                    $imagePaths[] = trim($img);
                }
            }
        }

        // 3. Check if user already had a review
        $existingReview = ProductReview::where('product_id', $validated['product_id'])
            ->where('user_id', Auth::id())
            ->first();

        // If form did not touch images, retain previous images
        if (empty($imagePaths) && !$request->has('existing_images') && !$request->hasFile('images') && $existingReview) {
            $imagePaths = $existingReview->images ?? [];
        }

        $cleanImages = !empty($imagePaths) ? array_values(array_unique($imagePaths)) : null;

        $review = ProductReview::updateOrCreate(
            [
                'product_id' => $validated['product_id'],
                'user_id' => Auth::id(),
            ],
            [
                'rating' => $validated['rating'],
                'title' => $validated['title'] ?? null,
                'comment' => $validated['comment'],
                'images' => $cleanImages,
                'status' => 'approved',
            ]
        );

        // Recalculate average rating for product
        $product = Product::find($validated['product_id']);
        if ($product) {
            $avg = ProductReview::where('product_id', $product->id)->where('status', 'approved')->avg('rating') ?: 0;
            $count = ProductReview::where('product_id', $product->id)->where('status', 'approved')->count();

            $product->update([
                'rating_avg' => round($avg, 2),
                'rating_count' => $count,
            ]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Your review has been submitted successfully!',
                'review' => $review->fresh(),
                'photos' => $review->photos,
            ]);
        }

        return back()->with('success', 'Your review with photos has been submitted. Thank you for your feedback!');
    }

    public function vote(Request $request, $id)
    {
        $validated = $request->validate([
            'vote_type' => 'required|in:helpful,unhelpful',
        ]);

        $review = ProductReview::findOrFail($id);

        // Prevent authors from voting on their own reviews
        if ($review->user_id === Auth::id()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot vote on your own review.',
                ], 422);
            }
            return back()->with('error', 'You cannot vote on your own review.');
        }

        $existing = ReviewVote::where('review_id', $review->id)
            ->where('user_id', Auth::id())
            ->first();

        $userVote = null;
        $message = '';

        if ($existing) {
            if ($existing->vote_type === $validated['vote_type']) {
                // Toggling off the existing vote
                $existing->delete();
                if ($validated['vote_type'] === 'helpful') {
                    $review->decrement('helpful_votes');
                } else {
                    $review->decrement('unhelpful_votes');
                }
                $userVote = null;
                $message = 'Vote removed.';
            } else {
                // Switching from helpful to unhelpful or vice-versa
                if ($existing->vote_type === 'helpful') {
                    $review->decrement('helpful_votes');
                    $review->increment('unhelpful_votes');
                } else {
                    $review->decrement('unhelpful_votes');
                    $review->increment('helpful_votes');
                }
                $existing->update(['vote_type' => $validated['vote_type']]);
                $userVote = $validated['vote_type'];
                $message = 'Vote updated.';
            }
        } else {
            // Registering a brand new vote
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
            $userVote = $validated['vote_type'];
            $message = 'Thank you for your feedback!';
        }

        $review->refresh();

        // Safety clamp to ensure counts never sink below zero
        if ($review->helpful_votes < 0 || $review->unhelpful_votes < 0) {
            $review->update([
                'helpful_votes' => max(0, (int) $review->helpful_votes),
                'unhelpful_votes' => max(0, (int) $review->unhelpful_votes),
            ]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'helpful' => (int) $review->helpful_votes,
                'unhelpful' => (int) $review->unhelpful_votes,
                'user_vote' => $userVote,
            ]);
        }

        return back()->with('success', $message);
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
