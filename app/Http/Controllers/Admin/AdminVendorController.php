<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vendor;
use App\Models\VendorPayout;

class AdminVendorController extends Controller
{
    public function index(Request $request)
    {
        $query = Vendor::with('user')->withCount('products');

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($kyc = $request->input('kyc')) {
            $query->where('kyc_status', $kyc);
        }

        $vendors = $query->latest()->paginate(15)->withQueryString();
        $pendingPayouts = VendorPayout::where('status', 'pending')->with('vendor')->get();

        return view('admin.vendors.index', compact('vendors', 'pendingPayouts'));
    }

    public function show($id)
    {
        $vendor = Vendor::with(['user', 'products', 'payouts'])->findOrFail($id);
        return view('admin.vendors.show', compact('vendor'));
    }

    public function updateStatus(Request $request, $id)
    {
        $vendor = Vendor::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:active,suspended,pending',
            'commission_rate' => 'required|numeric|min:0|max:100',
            'kyc_status' => 'required|in:verified,pending,rejected',
        ]);

        $vendor->update($validated);

        return back()->with('success', "Vendor '{$vendor->shop_name}' settings and status updated.");
    }

    public function approvePayout($id)
    {
        $payout = VendorPayout::findOrFail($id);
        $payout->status = 'approved';
        $payout->processed_at = now();
        $payout->save();

        return back()->with('success', "Payout #{$payout->id} for $" . number_format($payout->amount, 2) . " approved.");
    }
}
