<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
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
            'avatar' => 'nullable|string|max:1000',
            'avatar_file' => 'nullable|image|mimes:jpeg,png,jpg,webp,avif,gif|max:5120',
            'remove_avatar' => 'nullable|boolean',
        ]);

        if ($request->boolean('remove_avatar')) {
            if ($user->avatar && str_starts_with($user->avatar, '/storage/avatars/')) {
                $oldPath = str_replace('/storage/', '', $user->avatar);
                Storage::disk('public')->delete($oldPath);
            }
            $user->avatar = null;
        } elseif ($request->hasFile('avatar_file') && $request->file('avatar_file')->isValid()) {
            if ($user->avatar && str_starts_with($user->avatar, '/storage/avatars/')) {
                $oldPath = str_replace('/storage/', '', $user->avatar);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('avatar_file')->store('avatars', 'public');
            $user->avatar = '/storage/' . $path;
        } elseif ($request->filled('avatar')) {
            $user->avatar = $request->input('avatar');
        }

        $user->name = $validated['name'];
        $user->phone = $validated['phone'] ?? null;
        $user->bio = $validated['bio'] ?? null;
        $user->save();

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'Profile Updated',
            'description' => 'Updated personal details and contact info',
        ]);

        return back()->with('success', 'Your personal profile has been updated.');
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar_file' => 'required|image|mimes:jpeg,png,jpg,webp,avif,gif|max:5120',
        ]);

        $user = Auth::user();

        if ($request->hasFile('avatar_file') && $request->file('avatar_file')->isValid()) {
            if ($user->avatar && str_starts_with($user->avatar, '/storage/avatars/')) {
                $oldPath = str_replace('/storage/', '', $user->avatar);
                Storage::disk('public')->delete($oldPath);
            }

            $path = $request->file('avatar_file')->store('avatars', 'public');
            $user->avatar = '/storage/' . $path;
            $user->save();

            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'Avatar Updated',
                'description' => 'Updated profile picture',
            ]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Profile picture updated successfully!',
                    'avatar_url' => $user->avatar_url,
                ]);
            }

            return back()->with('success', 'Profile picture updated successfully!');
        }

        return back()->withErrors(['avatar_file' => 'Failed to upload image. Please try again.']);
    }

    public function deleteAvatar(Request $request)
    {
        $user = Auth::user();

        if ($user->avatar && str_starts_with($user->avatar, '/storage/avatars/')) {
            $oldPath = str_replace('/storage/', '', $user->avatar);
            Storage::disk('public')->delete($oldPath);
        }

        $user->avatar = null;
        $user->save();

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'Avatar Removed',
            'description' => 'Reset profile picture to default',
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Profile picture reset to default.',
                'avatar_url' => $user->avatar_url,
            ]);
        }

        return back()->with('success', 'Profile picture reset to default.');
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
            'name' => 'nullable|string|max:255',
            'full_name' => 'nullable|string|max:255',
            'phone' => 'required|string|max:30',
            'type' => 'nullable|in:shipping,billing',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'nullable|string|max:100',
            'is_default' => 'nullable|boolean',
        ]);

        $recipientName = trim($request->input('name') ?: $request->input('full_name', ''));
        if (empty($recipientName)) {
            $recipientName = Auth::user()->name;
        }

        $type = $request->input('type') ?: 'shipping';
        $country = $request->input('country') ?: 'United States';
        $isDefault = $request->boolean('is_default');

        if ($isDefault) {
            Address::where('user_id', Auth::id())->where('type', $type)->update(['is_default' => false]);
        }

        Address::create([
            'user_id' => Auth::id(),
            'name' => $recipientName,
            'phone' => $validated['phone'],
            'type' => $type,
            'address_line1' => $validated['address_line1'],
            'address_line2' => $validated['address_line2'] ?? null,
            'city' => $validated['city'],
            'state' => $validated['state'],
            'postal_code' => $validated['postal_code'],
            'country' => $country,
            'is_default' => $isDefault,
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Address Added',
            'description' => "Added {$type} address for {$recipientName}",
        ]);

        return redirect()->route('profile.index', ['tab' => 'addresses'])->with('success', 'New address added to your address book.')->with('tab', 'addresses');
    }

    public function updateAddress(Request $request, $id)
    {
        $address = Address::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'full_name' => 'nullable|string|max:255',
            'phone' => 'required|string|max:30',
            'type' => 'nullable|in:shipping,billing',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'nullable|string|max:100',
            'is_default' => 'nullable|boolean',
        ]);

        $recipientName = trim($request->input('name') ?: $request->input('full_name', ''));
        if (empty($recipientName)) {
            $recipientName = $address->name ?: Auth::user()->name;
        }

        $type = $request->input('type') ?: $address->type ?: 'shipping';
        $country = $request->input('country') ?: $address->country ?: 'United States';
        $isDefault = $request->boolean('is_default');

        if ($isDefault) {
            Address::where('user_id', Auth::id())->where('type', $type)->update(['is_default' => false]);
        }

        $address->update([
            'name' => $recipientName,
            'phone' => $validated['phone'],
            'type' => $type,
            'address_line1' => $validated['address_line1'],
            'address_line2' => $validated['address_line2'] ?? null,
            'city' => $validated['city'],
            'state' => $validated['state'],
            'postal_code' => $validated['postal_code'],
            'country' => $country,
            'is_default' => $isDefault,
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Address Updated',
            'description' => "Updated {$type} address for {$recipientName}",
        ]);

        return redirect()->route('profile.index', ['tab' => 'addresses'])->with('success', 'Address details updated successfully.')->with('tab', 'addresses');
    }

    public function deleteAddress($id)
    {
        $addr = Address::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $addr->delete();

        return redirect()->route('profile.index', ['tab' => 'addresses'])->with('info', 'Address has been deleted.')->with('tab', 'addresses');
    }

    public function setDefaultAddress($id)
    {
        $addr = Address::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        Address::where('user_id', Auth::id())->where('type', $addr->type)->update(['is_default' => false]);
        $addr->update(['is_default' => true]);

        return redirect()->route('profile.index', ['tab' => 'addresses'])->with('success', 'Default address updated.')->with('tab', 'addresses');
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
