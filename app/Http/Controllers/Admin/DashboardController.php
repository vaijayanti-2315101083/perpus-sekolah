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
        // Chart Data: Monthly Transactions
        $monthlyData = [];
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $year = now()->year;
        
        $borrowsByMonth = Borrow::selectRaw('MONTH(borrowed_at) as month, COUNT(*) as total')
            ->whereYear('borrowed_at', $year)
            ->groupBy('month')
            ->pluck('total', 'month');
        
        for ($i = 1; $i <= 12; $i++) {
            $monthlyData[] = $borrowsByMonth->get($i, 0);
        }

        // Chart Data: Book Categories
        $categoryData = Book::selectRaw('category, COUNT(*) as total')
            ->whereNotNull('category')
            ->groupBy('category')
            ->orderByDesc('total')
            ->limit(6)
            ->get();

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

            // Financial
            'total_fines_collected' => Restore::where('is_paid', true)->sum('fine'),
            'total_fines_pending' => Restore::where('is_paid', false)->sum('fine'),

            // Recent Activities
            'recent_borrows' => Borrow::with(['user', 'book'])
                ->latest('borrowed_at')
                ->take(5)
                ->get(),
            
            'recent_returns' => Restore::with(['borrow.user', 'borrow.book'])
                ->latest('returned_at')
                ->take(5)
                ->get(),

            // Books that need attention
            'low_stock_books' => Book::where('amount', '<=', 2)
                ->where('amount', '>', 0)
                ->take(5)
                ->get(),
            
            'out_of_stock_books' => Book::where('amount', 0)->count(),

            // Chart Data (existing)
            'chart_months' => $months,
            'chart_monthly_data' => $monthlyData,
            'chart_categories' => $categoryData->pluck('category')->toArray(),
            'chart_category_data' => $categoryData->pluck('total')->toArray(),

            // Chart 3: Return Status Distribution
            'chart_return_status' => [
                'labels' => ['Belum Dikonfirmasi', 'Terlambat', 'Denda Belum Dibayar', 'Dikembalikan'],
                'data' => [
                    Restore::where('status', Restore::STATUSES['Not confirmed'])->count(),
                    Restore::where('status', Restore::STATUSES['Past due'])->count(),
                    Restore::where('status', Restore::STATUSES['Fine not paid'])->count(),
                    Restore::where('status', Restore::STATUSES['Returned'])->count(),
                ],
            ],

            // Chart 4: Monthly Fines Collection
            'chart_monthly_fines' => $this->getMonthlyFinesData($year, $months),
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

    /**
     * Get monthly fines collection data for chart
     */
    protected function getMonthlyFinesData(int $year, array $months): array
    {
        $finesByMonth = Restore::selectRaw('MONTH(returned_at) as month, SUM(fine) as total')
            ->whereYear('returned_at', $year)
            ->where('is_paid', true)
            ->groupBy('month')
            ->pluck('total', 'month');

        $data = [];
        for ($i = 1; $i <= 12; $i++) {
            $data[] = (int) $finesByMonth->get($i, 0);
        }

        return [
            'labels' => $months,
            'data' => $data,
        ];
    }
}