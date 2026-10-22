<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StoreSetting;
use App\Models\User;
use App\Models\ActivityLog;

class AdminSettingsController extends Controller
{
    public function index()
    {
        $settings = [
            'store_name' => StoreSetting::get('store_name', 'SHOPICART'),
            'store_tagline' => StoreSetting::get('store_tagline', 'The Precision Commerce Platform'),
            'store_email' => StoreSetting::get('store_email', 'contact@shopicart.com'),
            'store_phone' => StoreSetting::get('store_phone', '+1 (800) 892-0199'),
            'store_address' => StoreSetting::get('store_address', '100 Silicon Way, San Francisco, CA 94107'),
            'currency_symbol' => StoreSetting::get('currency_symbol', '$'),
            'currency_code' => StoreSetting::get('currency_code', 'USD'),
            'tax_rate' => StoreSetting::get('tax_rate', '8.5'),
            'shipping_standard_rate' => StoreSetting::get('shipping_standard_rate', '12.00'),
            'shipping_express_rate' => StoreSetting::get('shipping_express_rate', '25.00'),
            'free_shipping_threshold' => StoreSetting::get('free_shipping_threshold', '99.00'),
        ];

        $adminUsers = User::where('role', 'admin')->get();
        $activityLogs = ActivityLog::with('user')->latest()->take(20)->get();

        return view('admin.settings.index', compact('settings', 'adminUsers', 'activityLogs'));
    }

    public function update(Request $request)
    {
        $keys = [
            'store_name',
            'store_tagline',
            'store_email',
            'store_phone',
            'store_address',
            'currency_symbol',
            'currency_code',
            'tax_rate',
            'shipping_standard_rate',
            'shipping_express_rate',
            'free_shipping_threshold',
        ];

        foreach ($keys as $k) {
            if ($request->has($k)) {
                StoreSetting::set($k, $request->input($k));
            }
        }

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Settings Updated',
            'description' => 'Platform store configuration parameters modified',
        ]);

        return back()->with('success', 'Store configuration and tax/shipping rates saved.');
    }
}
