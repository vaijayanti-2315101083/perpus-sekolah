<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Borrow;
use App\Models\Restore;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportApiController extends Controller
{
    /**
     * GET /api/dashboard
     */
    public function dashboard()
    {
        return response()->json([
            'data' => [
                'books' => [
                    'total' => Book::count(),
                    'available' => Book::where('status', 'Available')->count(),
                    'unavailable' => Book::where('status', 'Unavailable')->count(),
                    'out_of_stock' => Book::where('amount', 0)->count(),
                ],
                'users' => [
                    'total' => User::count(),
                    'members' => User::where('role', 'Member')->count(),
                    'admins' => User::where('role', 'Admin')->count(),
                    'librarians' => User::whereIn('role', ['Librarian', 'Pustakawan'])->count(),
                ],
                'borrows' => [
                    'total' => Borrow::count(),
                    'pending' => Borrow::where('confirmation', false)->count(),
                    'confirmed' => Borrow::where('confirmation', true)->count(),
                ],
                'returns' => [
                    'total' => Restore::count(),
                    'pending' => Restore::whereIn('status', [Restore::STATUSES['Not confirmed'], Restore::STATUSES['Past due']])->count(),
                    'unpaid_fines' => Restore::where('status', Restore::STATUSES['Fine not paid'])->count(),
                    'completed' => Restore::where('status', Restore::STATUSES['Returned'])->count(),
                ],
                'fines' => [
                    'total_collected' => Restore::where('is_paid', true)->sum('fine'),
                    'total_pending' => Restore::where('is_paid', false)->sum('fine'),
                ],
            ]
        ]);
    }

    /**
     * GET /api/reports
     */
    public function index(Request $request)
    {
        $request->validate([
            'start_date' => ['sometimes', 'date'],
            'end_date' => ['sometimes', 'date', 'after_or_equal:start_date'],
        ]);

        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : now()->endOfDay();

        $borrows = Borrow::with(['book', 'user'])
            ->whereBetween('borrowed_at', [$startDate, $endDate])
            ->get();

        $returns = Restore::with(['borrow.book', 'borrow.user'])
            ->whereBetween('returned_at', [$startDate, $endDate])
            ->get();

        return response()->json([
            'period' => [
                'start' => $startDate->toDateString(),
                'end' => $endDate->toDateString(),
            ],
            'summary' => [
                'total_borrows' => $borrows->count(),
                'confirmed_borrows' => $borrows->where('confirmation', true)->count(),
                'total_returns' => $returns->count(),
                'total_fines' => $returns->sum('fine'),
                'fines_paid' => $returns->where('is_paid', true)->sum('fine'),
                'fines_pending' => $returns->where('is_paid', false)->sum('fine'),
            ],
            'data' => [
                'borrows' => $borrows,
                'returns' => $returns,
            ]
        ]);
    }

    /**
     * GET /api/reports/monthly
     */
    public function monthly(Request $request)
    {
        $year = $request->year ?? now()->year;

        $monthlyData = Borrow::selectRaw('MONTH(borrowed_at) as month, COUNT(*) as total')
            ->whereYear('borrowed_at', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $data = [];
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        
        for ($i = 1; $i <= 12; $i++) {
            $data[] = [
                'month' => $months[$i - 1],
                'total' => $monthlyData->get($i)->total ?? 0
            ];
        }

        return response()->json([
            'year' => $year,
            'data' => $data
        ]);
    }

    /**
     * GET /api/reports/categories
     */
    public function categories()
    {
        $categories = Book::selectRaw('category, COUNT(*) as total, SUM(amount) as stock')
            ->whereNotNull('category')
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        $totalBooks = Book::count();

        $data = $categories->map(function ($cat) use ($totalBooks) {
            return [
                'category' => $cat->category,
                'total' => $cat->total,
                'stock' => $cat->stock,
                'percentage' => $totalBooks > 0 ? round(($cat->total / $totalBooks) * 100, 2) : 0
            ];
        });

        return response()->json([
            'total_books' => $totalBooks,
            'data' => $data
        ]);
    }

    /**
     * GET /api/reports/fines
     */
    public function fines(Request $request)
    {
        $request->validate([
            'start_date' => ['sometimes', 'date'],
            'end_date' => ['sometimes', 'date'],
        ]);

        $query = Restore::with(['borrow.book', 'borrow.user'])
            ->where('fine', '>', 0);

        if ($request->start_date) {
            $query->whereDate('returned_at', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->whereDate('returned_at', '<=', $request->end_date);
        }

        $fines = $query->latest('returned_at')->get();

        return response()->json([
            'summary' => [
                'total' => $fines->sum('fine'),
                'paid' => $fines->where('is_paid', true)->sum('fine'),
                'pending' => $fines->where('is_paid', false)->sum('fine'),
                'count' => $fines->count(),
            ],
            'data' => $fines
        ]);
    }
}
