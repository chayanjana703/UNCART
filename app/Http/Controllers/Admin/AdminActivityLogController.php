<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ActivityLog;

class AdminActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user');

        if ($search = $request->input('q')) {
            $query->where('action', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }

        if ($action = $request->input('action_type')) {
            $query->where('action', 'like', "%{$action}%");
        }

        $logs = $query->latest()->paginate(20)->withQueryString();

        $actionTypes = [
            'Order' => 'Orders & Commerce',
            'Vendor' => 'Vendor Operations',
            'User' => 'User & Auth',
            'Return' => 'Returns & Disputes',
            'Product' => 'Product Catalog',
        ];

        return view('admin.activity-logs.index', compact('logs', 'actionTypes'));
    }

    public function exportCsv()
    {
        $logs = ActivityLog::with('user')->latest()->take(500)->get();

        $csv = "Timestamp,User,Email,Role,Action,Description,IP Address\n";
        foreach ($logs as $log) {
            $uName = $log->user ? str_replace('"', '""', $log->user->name) : 'System / Guest';
            $uEmail = $log->user ? $log->user->email : 'N/A';
            $uRole = $log->user ? $log->user->role : 'system';
            $action = str_replace('"', '""', $log->action);
            $desc = str_replace('"', '""', $log->description);
            $time = $log->created_at ? $log->created_at->format('Y-m-d H:i:s') : '';
            $csv .= "\"{$time}\",\"{$uName}\",\"{$uEmail}\",\"{$uRole}\",\"{$action}\",\"{$desc}\",\"{$log->ip_address}\"\n";
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="shopicart_activity_logs_' . date('Y_m_d') . '.csv"',
        ]);
    }
}
