<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\StoreSetting;

class AdminPaymentController extends Controller
{
    public function index()
    {
        $transactions = Order::whereNotNull('transaction_id')
            ->with('user')
            ->latest()
            ->paginate(15);

        $paymentMethods = [
            'card' => StoreSetting::get('payment_method_card_enabled', true),
            'upi' => StoreSetting::get('payment_method_upi_enabled', true),
            'wallet' => StoreSetting::get('payment_method_wallet_enabled', true),
            'cod' => StoreSetting::get('payment_method_cod_enabled', true),
        ];

        return view('admin.payments.index', compact('transactions', 'paymentMethods'));
    }

    public function toggleMethod(Request $request, $method)
    {
        $key = "payment_method_{$method}_enabled";
        $current = StoreSetting::get($key, true);
        StoreSetting::set($key, !$current);

        return back()->with('success', "Payment method '{$method}' updated.");
    }
}
