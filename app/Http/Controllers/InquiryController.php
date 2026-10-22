<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CustomerInquiry;
use App\Models\Vendor;
use App\Models\Notification;

class InquiryController extends Controller
{
    public function index()
    {
        $inquiries = CustomerInquiry::where('user_id', Auth::id())
            ->with(['vendor', 'product'])
            ->latest()
            ->paginate(10);

        return view('pages.inquiries', compact('inquiries'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'product_id' => 'nullable|exists:products,id',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
        ]);

        $inquiry = CustomerInquiry::create([
            'user_id' => Auth::id(),
            'vendor_id' => $validated['vendor_id'],
            'product_id' => $validated['product_id'] ?? null,
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'status' => 'open',
        ]);

        $vendor = Vendor::find($validated['vendor_id']);
        if ($vendor && $vendor->user) {
            Notification::create([
                'user_id' => $vendor->user->id,
                'title' => 'New Customer Inquiry',
                'message' => Auth::user()->name . " sent a question: \"{$validated['subject']}\"",
                'type' => 'vendor',
                'link' => route('vendor.inquiries'),
            ]);
        }

        return back()->with('success', 'Your inquiry has been transmitted to the seller.');
    }
}
