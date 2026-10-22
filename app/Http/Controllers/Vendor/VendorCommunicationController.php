<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Vendor;
use App\Models\CustomerInquiry;
use App\Models\ProductReview;
use App\Models\Notification;

class VendorCommunicationController extends Controller
{
    private function getVendor(): Vendor
    {
        return Vendor::where('user_id', Auth::id())->firstOrFail();
    }

    public function inquiries()
    {
        $vendor = $this->getVendor();

        $inquiries = CustomerInquiry::where('vendor_id', $vendor->id)
            ->with(['user', 'product'])
            ->latest()
            ->paginate(12);

        return view('vendor.communication.inquiries', compact('inquiries', 'vendor'));
    }

    public function replyInquiry(Request $request, $id)
    {
        $vendor = $this->getVendor();

        $request->validate([
            'reply' => 'required|string|max:2000',
        ]);

        $inquiry = CustomerInquiry::where('id', $id)
            ->where('vendor_id', $vendor->id)
            ->firstOrFail();

        $inquiry->reply = $request->input('reply');
        $inquiry->status = 'replied';
        $inquiry->save();

        Notification::create([
            'user_id' => $inquiry->user_id,
            'title' => "Reply from {$vendor->shop_name}",
            'message' => "The vendor replied to your inquiry: \"{$inquiry->subject}\"",
            'type' => 'vendor',
            'link' => route('inquiries.index'),
        ]);

        return back()->with('success', 'Your reply has been dispatched to the customer.');
    }

    public function reviews()
    {
        $vendor = $this->getVendor();

        $reviews = ProductReview::whereIn('product_id', function ($query) use ($vendor) {
            $query->select('id')->from('products')->where('vendor_id', $vendor->id);
        })->with(['product', 'user'])->latest()->paginate(12);

        return view('vendor.communication.reviews', compact('reviews', 'vendor'));
    }

    public function replyReview(Request $request, $id)
    {
        $vendor = $this->getVendor();

        $request->validate([
            'vendor_reply' => 'required|string|max:1000',
        ]);

        $review = ProductReview::where('id', $id)
            ->whereIn('product_id', function ($query) use ($vendor) {
                $query->select('id')->from('products')->where('vendor_id', $vendor->id);
            })->firstOrFail();

        $review->vendor_reply = $request->input('vendor_reply');
        $review->vendor_replied_at = now();
        $review->save();

        Notification::create([
            'user_id' => $review->user_id,
            'title' => "Seller Replied to Your Review",
            'message' => "{$vendor->shop_name} replied to your review on {$review->product->name}",
            'type' => 'vendor',
            'link' => route('products.show', $review->product->slug),
        ]);

        return back()->with('success', 'Your official seller response has been published under this review.');
    }
}
