<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActivityLogController extends Controller
{
    /**
     * Display activity logs
     */
    public function index(Request $request)
    {
        $query = DB::table('activity_logs')
            ->select('activity_logs.*')
            ->orderBy('created_at', 'desc');

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('user_role', $request->role);
        }

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by subject type
        if ($request->filled('subject_type')) {
            $query->where('subject_type', $request->subject_type);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('user_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('subject_name', 'like', "%{$search}%");
            });
        }

        $logs = $query->paginate(50);

        // Get filter options
        $users = DB::table('activity_logs')
            ->select('user_id', 'user_name')
            ->distinct()
            ->whereNotNull('user_id')
            ->orderBy('user_name')
            ->get();

        $actions = DB::table('activity_logs')
            ->select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        $subjectTypes = DB::table('activity_logs')
            ->select('subject_type')
            ->distinct()
            ->whereNotNull('subject_type')
            ->orderBy('subject_type')
            ->pluck('subject_type');

        return view('content.admin.activity.index', compact('logs', 'users', 'actions', 'subjectTypes'));
    }

    /**
     * Get activity statistics
     */
    public function statistics(Request $request)
    {
        $period = $request->get('period', '7days');

        $dateFrom = match($period) {
            '24hours' => now()->subDay(),
            '7days' => now()->subDays(7),
            '30days' => now()->subDays(30),
            '90days' => now()->subDays(90),
            default => now()->subDays(7)
        };

        // Activities by action
        $byAction = DB::table('activity_logs')
            ->select('action', DB::raw('count(*) as count'))
            ->where('created_at', '>=', $dateFrom)
            ->groupBy('action')
            ->get();

        // Activities by user role
        $byRole = DB::table('activity_logs')
            ->select('user_role', DB::raw('count(*) as count'))
            ->where('created_at', '>=', $dateFrom)
            ->whereNotNull('user_role')
            ->groupBy('user_role')
            ->get();

        // Activities by subject type
        $bySubject = DB::table('activity_logs')
            ->select('subject_type', DB::raw('count(*) as count'))
            ->where('created_at', '>=', $dateFrom)
            ->whereNotNull('subject_type')
            ->groupBy('subject_type')
            ->get();

        // Most active users
        $mostActive = DB::table('activity_logs')
            ->select('user_id', 'user_name', 'user_role', DB::raw('count(*) as activity_count'))
            ->where('created_at', '>=', $dateFrom)
            ->whereNotNull('user_id')
            ->groupBy('user_id', 'user_name', 'user_role')
            ->orderBy('activity_count', 'desc')
            ->limit(10)
            ->get();

        // Daily activity trend
        $dailyTrend = DB::table('activity_logs')
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as count')
            )
            ->where('created_at', '>=', $dateFrom)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'by_action' => $byAction,
            'by_role' => $byRole,
            'by_subject' => $bySubject,
            'most_active' => $mostActive,
            'daily_trend' => $dailyTrend
        ]);
    }

    /**
     * Clear old activity logs
     */
    public function clearOldLogs(Request $request)
    {
        $days = $request->get('days', 90);
        
        $deleted = DB::table('activity_logs')
            ->where('created_at', '<', now()->subDays($days))
            ->delete();

        return response()->json([
            'success' => true,
            'message' => "Deleted {$deleted} old activity logs"
        ]);
    }

    /**
     * Export activity logs
     */
    public function export(Request $request)
    {
        $query = DB::table('activity_logs')
            ->orderBy('created_at', 'desc');

        // Apply same filters as index
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('role')) {
            $query->where('user_role', $request->role);
        }
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->limit(5000)->get();

        // Generate CSV
        $filename = 'activity-logs-' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            
            // Headers
            fputcsv($file, ['Date', 'User', 'Role', 'Action', 'Subject Type', 'Subject', 'Description', 'IP Address']);

            // Data
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->created_at,
                    $log->user_name ?? 'System',
                    $log->user_role ?? 'N/A',
                    $log->action,
                    $log->subject_type ?? 'N/A',
                    $log->subject_name ?? 'N/A',
                    $log->description,
                    $log->ip_address ?? 'N/A'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}