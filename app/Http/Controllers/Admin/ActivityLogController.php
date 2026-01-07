<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of activity logs.
     */
    public function index(Request $request)
    {
        $logs = ActivityLog::with('user');

        // Filter by action
        if ($request->filled('action')) {
            $logs->where('action', $request->action);
        }

        // Filter by model type
        if ($request->filled('model_type')) {
            $logs->where('model_type', $request->model_type);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $logs->where('user_id', $request->user_id);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $logs->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $logs->whereDate('created_at', '<=', $request->end_date);
        }

        // Search in description
        if ($request->filled('search')) {
            $logs->where('description', 'LIKE', "%{$request->search}%");
        }

        $logs = $logs->latest()->paginate(20);

        // Get unique model types for filter dropdown
        $modelTypes = ActivityLog::distinct()->pluck('model_type')->filter();
        $actions = ['create', 'update', 'delete', 'login', 'logout'];

        return view('admin.activity-logs.index', [
            'logs' => $logs,
            'modelTypes' => $modelTypes,
            'actions' => $actions,
        ]);
    }

    /**
     * Display the specified activity log detail.
     */
    public function show($id)
    {
        $log = ActivityLog::with('user')->findOrFail($id);

        return view('admin.activity-logs.show', [
            'log' => $log,
        ]);
    }
}
