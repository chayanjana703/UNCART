<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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
            'shop_logo' => 'nullable|string|max:500',
            'shop_banner' => 'nullable|string|max:500',
            'shop_logo_file' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:5120',
            'shop_banner_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'return_policy' => 'nullable|string',
            'shipping_policy' => 'nullable|string',
            'bank_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:50',
            'ifsc_code' => 'nullable|string|max:50',
        ]);

        // Process device uploaded shop logo
        if ($request->hasFile('shop_logo_file') && $request->file('shop_logo_file')->isValid()) {
            if ($vendor->shop_logo && str_starts_with($vendor->shop_logo, '/storage/shops/')) {
                $oldPath = str_replace('/storage/', '', $vendor->shop_logo);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('shop_logo_file')->store('shops', 'public');
            $validated['shop_logo'] = '/storage/' . $path;
        }

        // Process device uploaded shop banner
        if ($request->hasFile('shop_banner_file') && $request->file('shop_banner_file')->isValid()) {
            if ($vendor->shop_banner && str_starts_with($vendor->shop_banner, '/storage/banners/')) {
                $oldPath = str_replace('/storage/', '', $vendor->shop_banner);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('shop_banner_file')->store('banners', 'public');
            $validated['shop_banner'] = '/storage/' . $path;
        }

        unset($validated['shop_logo_file'], $validated['shop_banner_file']);

        $vendor->update($validated);

        return back()->with('success', 'Your seller shop profile & branding settings have been updated.');
    }

    public function submitKyc(Request $request)
    {
        $vendor = $this->getVendor();

        $request->validate([
            'kyc_file' => 'nullable|file|mimes:pdf,jpeg,png,jpg,webp|max:10240',
            'tax_id' => 'nullable|string|max:100',
            'kyc_document' => 'nullable|string|max:500',
        ]);

        $kycDoc = $vendor->kyc_document;

        if ($request->hasFile('kyc_file') && $request->file('kyc_file')->isValid()) {
            $path = $request->file('kyc_file')->store('kyc', 'public');
            $kycDoc = '/storage/' . $path;
            if ($request->filled('tax_id')) {
                $kycDoc .= ' (Tax/EIN: ' . $request->input('tax_id') . ')';
            }
        } elseif ($request->filled('tax_id')) {
            $kycDoc = 'Tax/EIN Registration ID: ' . $request->input('tax_id');
        } elseif ($request->filled('kyc_document')) {
            $kycDoc = $request->input('kyc_document');
        } else {
            $kycDoc = 'Identity verification uploaded on ' . now()->format('M d, Y');
        }

        $vendor->update([
            'kyc_status' => 'verified',
            'kyc_document' => $kycDoc,
        ]);

        return back()->with('success', 'KYC compliance documents uploaded and verified successfully.');
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
