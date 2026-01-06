<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Borrow;
use App\Models\Restore;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display dashboard based on user role
     */
    public function index()
    {
        $user = Auth::user();

        // Admin Dashboard
        if ($user->role === User::ROLES['Admin']) {
            return $this->adminDashboard();
        }

        // Pustakawan Dashboard
        if ($user->role === User::ROLES['Librarian']) {
            return $this->pustakawanDashboard();
        }

        // Member Dashboard (redirect to home)
        return redirect()->route('home');
    }

    /**
     * Admin Dashboard - Full Control View
     */
    protected function adminDashboard()
    {
        $data = [
            // Books Statistics
            'total_books' => Book::count(),
            'available_books' => Book::where('status', Book::STATUSES['Available'])->count(),
            'unavailable_books' => Book::where('status', Book::STATUSES['Unavailable'])->count(),

            // User Statistics (Admin can see all)
            'total_users' => User::count(),
            'total_admins' => User::where('role', User::ROLES['Admin'])->count(),
            'total_librarians' => User::where('role', User::ROLES['Librarian'])->count(),
            'total_members' => User::where('role', User::ROLES['Member'])->count(),

            // Borrow Statistics
            'pending_borrows' => Borrow::where('confirmation', false)->count(),
            'confirmed_borrows' => Borrow::where('confirmation', true)->count(),
            'total_borrows' => Borrow::count(),

            // Return Statistics
            'pending_returns' => Restore::where('status', Restore::STATUSES['Not confirmed'])->count(),
            'overdue_returns' => Restore::where('status', Restore::STATUSES['Past due'])->count(),
            'unpaid_fines' => Restore::where('status', Restore::STATUSES['Fine not paid'])->count(),
            'completed_returns' => Restore::where('status', Restore::STATUSES['Returned'])->count(),

            // Recent Activities
            'recent_borrows' => Borrow::with(['user', 'book'])
                ->latest('borrowed_at')
                ->take(5)
                ->get(),
            
            'recent_returns' => Restore::with(['user', 'book'])
                ->latest('returned_at')
                ->take(5)
                ->get(),

            // Books that need attention
            'low_stock_books' => Book::where('amount', '<=', 2)
                ->where('amount', '>', 0)
                ->take(5)
                ->get(),
            
            'out_of_stock_books' => Book::where('amount', 0)->count(),
        ];

        return view('admin.dashboard-admin', $data);
    }

    /**
     * Pustakawan Dashboard - Operational View
     */
    protected function pustakawanDashboard()
    {
        $data = [
            // Books Statistics (operational focus)
            'total_books' => Book::count(),
            'available_books' => Book::where('status', Book::STATUSES['Available'])->count(),
            'low_stock_books_count' => Book::where('amount', '<=', 2)->where('amount', '>', 0)->count(),

            // Member Statistics only (cannot see admin/librarian)
            'total_members' => User::where('role', User::ROLES['Member'])->count(),

            // Pending Tasks (what needs action NOW)
            'pending_borrow_confirmations' => Borrow::where('confirmation', false)->count(),
            'pending_return_confirmations' => Restore::where('status', Restore::STATUSES['Not confirmed'])->count(),
            'overdue_to_process' => Restore::where('status', Restore::STATUSES['Past due'])->count(),
            'fines_to_collect' => Restore::where('status', Restore::STATUSES['Fine not paid'])->count(),

            // Today's Activity
            'borrows_today' => Borrow::whereDate('borrowed_at', today())->count(),
            'returns_today' => Restore::whereDate('returned_at', today())->count(),

            // Active Operations
            'active_borrows' => Borrow::where('confirmation', true)
                ->whereDoesntHave('restore')
                ->count(),

            // Recent Pending Actions (tasks to handle)
            'recent_pending_borrows' => Borrow::with(['user', 'book'])
                ->where('confirmation', false)
                ->latest('borrowed_at')
                ->take(5)
                ->get(),
            
            'recent_pending_returns' => Restore::with(['user', 'book'])
                ->whereIn('status', [
                    Restore::STATUSES['Not confirmed'],
                    Restore::STATUSES['Past due'],
                    Restore::STATUSES['Fine not paid']
                ])
                ->latest('returned_at')
                ->take(5)
                ->get(),

            // Books that need restocking
            'low_stock_books' => Book::where('amount', '<=', 2)
                ->where('amount', '>', 0)
                ->orderBy('amount', 'asc')
                ->take(5)
                ->get(),
        ];

        return view('admin.dashboard-pustakawan', $data);
    }
}