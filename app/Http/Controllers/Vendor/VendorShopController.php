<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Vendor;
use App\Models\VendorPayout;

class VendorShopController extends Controller
{
    private function getVendor(): Vendor
    {
        return Vendor::where('user_id', Auth::id())->firstOrFail();
    }

    public function edit()
    {
        $vendor = $this->getVendor();
        $payouts = VendorPayout::where('vendor_id', $vendor->id)->latest()->take(10)->get();

        return view('vendor.shop.edit', compact('vendor', 'payouts'));
    }

    public function update(Request $request)
    {
        $vendor = $this->getVendor();

        $validated = $request->validate([
            'shop_name' => 'required|string|max:255',
            'shop_description' => 'nullable|string|max:1000',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'shop_logo' => 'nullable|string|url',
            'shop_banner' => 'nullable|string|url',
            'return_policy' => 'nullable|string',
            'shipping_policy' => 'nullable|string',
            'bank_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:50',
            'ifsc_code' => 'nullable|string|max:50',
        ]);

        $vendor->update($validated);

        return back()->with('success', 'Your seller shop profile & bank settings have been updated.');
    }

    public function submitKyc(Request $request)
    {
        $vendor = $this->getVendor();

        $request->validate([
            'kyc_document' => 'nullable|string|url',
        ]);

        $vendor->update([
            'kyc_status' => 'verified', // Simulation instant verification for smooth demo
            'kyc_document' => $request->input('kyc_document', 'https://example.com/kyc-verified.pdf'),
        ]);

        return back()->with('success', 'KYC identity documents submitted and verified.');
    }

    public function requestPayout(Request $request)
    {
        $vendor = $this->getVendor();

        $request->validate([
            'amount' => 'required|numeric|min:50',
        ]);

        $amount = (float) $request->input('amount');

        VendorPayout::create([
            'vendor_id' => $vendor->id,
            'amount' => $amount,
            'status' => 'approved', // For testing demo
            'reference_note' => 'Payout initiated to bank account: ' . ($vendor->account_number ?: 'Primary Bank'),
            'processed_at' => now(),
        ]);

        $vendor->increment('total_payouts', $amount);

        return back()->with('success', "Payout request for $" . number_format($amount, 2) . " processed successfully.");
    }
}
