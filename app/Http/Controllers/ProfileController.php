<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Address;
use App\Models\ActivityLog;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $addresses = $user->addresses;
        $recentOrders = $user->orders()->latest()->take(5)->get();

        return view('pages.profile', compact('user', 'addresses', 'recentOrders'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:500',
            'avatar' => 'nullable|string|url',
        ]);

        $user->update($validated);

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'Profile Updated',
            'description' => 'Updated personal details and contact info',
        ]);

        return back()->with('success', 'Your personal profile has been updated.');
    }

    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'The current password provided is incorrect.']);
        }

        $user->password = Hash::make($validated['password']);
        $user->save();

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'Password Changed',
        ]);

        return back()->with('success', 'Your password has been changed successfully.');
    }

    public function toggle2FA(Request $request)
    {
        $user = Auth::user();
        $user->two_factor_enabled = !$user->two_factor_enabled;
        $user->save();

        $status = $user->two_factor_enabled ? 'enabled' : 'disabled';
        return back()->with('success', "Two-Factor Authentication is now {$status}.");
    }

    public function storeAddress(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'type' => 'required|in:shipping,billing',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'is_default' => 'nullable|boolean',
        ]);

        $isDefault = $request->boolean('is_default');

        if ($isDefault) {
            Address::where('user_id', Auth::id())->where('type', $validated['type'])->update(['is_default' => false]);
        }

        Address::create([
            'user_id' => Auth::id(),
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'type' => $validated['type'],
            'address_line1' => $validated['address_line1'],
            'address_line2' => $validated['address_line2'] ?? null,
            'city' => $validated['city'],
            'state' => $validated['state'],
            'postal_code' => $validated['postal_code'],
            'country' => $validated['country'],
            'is_default' => $isDefault,
        ]);

        return back()->with('success', 'New address added to your address book.');
    }

    public function deleteAddress($id)
    {
        $addr = Address::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $addr->delete();

        return back()->with('info', 'Address has been deleted.');
    }

    public function setDefaultAddress($id)
    {
        $addr = Address::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        Address::where('user_id', Auth::id())->where('type', $addr->type)->update(['is_default' => false]);
        $addr->update(['is_default' => true]);

        return back()->with('success', 'Default address updated.');
    }

    public function preferences(Request $request)
    {
        $user = Auth::user();
        $prefs = $user->preferences ?? [];

        $prefs['currency'] = $request->input('currency', 'USD');
        $prefs['order_notifications'] = $request->boolean('order_notifications');
        $prefs['promo_notifications'] = $request->boolean('promo_notifications');

        $user->preferences = $prefs;
        $user->save();

        return back()->with('success', 'Preferences saved.');
    }
}
