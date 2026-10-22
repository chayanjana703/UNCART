<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\ActivityLog;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($role = $request->input('role')) {
            $query->where('role', $role);
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:30',
            'role' => 'required|in:customer,vendor,admin',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
            'email_verified_at' => now(),
            'avatar' => 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=400&auto=format&fit=crop&q=80',
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Admin Created User',
            'description' => "Created {$validated['role']} account for {$validated['email']}",
        ]);

        return back()->with('success', "User '{$user->name}' created successfully.");
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => "required|email|unique:users,email,{$user->id}",
            'phone' => 'nullable|string|max:30',
            'role' => 'required|in:customer,vendor,admin',
            'password' => 'nullable|string|min:8',
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'role' => $validated['role'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return back()->with('success', "User {$user->name} updated successfully.");
    }

    public function toggleBlock($id)
    {
        $user = User::findOrFail($id);
        $user->is_blocked = !$user->is_blocked;
        $user->save();

        $action = $user->is_blocked ? 'blocked' : 'unblocked';

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => "User {$action}",
            'description' => "User {$user->email} was {$action} by admin",
        ]);

        return back()->with('success', "User account {$user->name} has been {$action}.");
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own active administrator account.');
        }

        $user->delete();

        return back()->with('info', 'User account permanently removed.');
    }

    public function exportCsv()
    {
        $users = User::latest()->get();

        $csv = "ID,Name,Email,Phone,Role,Status,Created At\n";
        foreach ($users as $u) {
            $status = $u->is_blocked ? 'Blocked' : 'Active';
            $csv .= "{$u->id},\"{$u->name}\",\"{$u->email}\",\"{$u->phone}\",\"{$u->role}\",\"{$status}\",\"{$u->created_at}\"\n";
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="shopicart_users_' . date('Y_m_d') . '.csv"',
        ]);
    }
}
