<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductReview;

class AdminReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = ProductReview::with(['product', 'user']);

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $reviews = $query->latest()->paginate(15)->withQueryString();

        return view('admin.reviews.index', compact('reviews'));
    }

    public function updateStatus(Request $request, $id)
    {
        $review = ProductReview::findOrFail($id);
        $status = $request->input('status', 'approved');

        $review->status = $status;
        $review->save();

        return back()->with('success', "Review marked as {$status}.");
    }

    public function destroy($id)
    {
        $review = ProductReview::findOrFail($id);
        $review->delete();

        return back()->with('info', "Review removed.");
    }
}
