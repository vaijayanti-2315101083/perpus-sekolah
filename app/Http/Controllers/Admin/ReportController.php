<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Borrow;
use App\Models\Restore;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Display reports with optional date filter
     * Default: show all data (no date filter)
     */
    public function index(Request $request)
    {
        $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        // Check if user has applied date filter
        $hasDateFilter = $request->has('start_date') || $request->has('end_date');

        $startDate = $request->start_date 
            ? Carbon::parse($request->start_date)->startOfDay() 
            : null;
        $endDate = $request->end_date 
            ? Carbon::parse($request->end_date)->endOfDay() 
            : null;

        // Get borrows - with optional date filter
        $borrowsQuery = Borrow::with(['book', 'user', 'restore']);
        
        if ($hasDateFilter) {
            if ($startDate && $endDate) {
                $borrowsQuery->whereBetween('borrowed_at', [$startDate, $endDate]);
            } elseif ($startDate) {
                $borrowsQuery->where('borrowed_at', '>=', $startDate);
            } elseif ($endDate) {
                $borrowsQuery->where('borrowed_at', '<=', $endDate);
            }
        }
        
        $borrows = $borrowsQuery->latest('borrowed_at')->get();

        // Get returns - with optional date filter
        $returnsQuery = Restore::with(['borrow.book', 'borrow.user']);
        
        if ($hasDateFilter) {
            if ($startDate && $endDate) {
                $returnsQuery->whereBetween('returned_at', [$startDate, $endDate]);
            } elseif ($startDate) {
                $returnsQuery->where('returned_at', '>=', $startDate);
            } elseif ($endDate) {
                $returnsQuery->where('returned_at', '<=', $endDate);
            }
        }
        
        $returns = $returnsQuery->latest('returned_at')->get();

        // Calculate summaries
        $summary = [
            'total_borrows' => $borrows->count(),
            'confirmed_borrows' => $borrows->where('confirmation', true)->count(),
            'pending_borrows' => $borrows->where('confirmation', false)->count(),
            'total_returns' => $returns->count(),
            'total_fines' => $returns->sum('fine'),
            'fines_paid' => $returns->where('is_paid', true)->sum('fine'),
            'fines_pending' => $returns->where('is_paid', false)->sum('fine'),
        ];

        return view('admin.reports.index', [
            'borrows' => $borrows,
            'returns' => $returns,
            'summary' => $summary,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'hasDateFilter' => $hasDateFilter,
        ]);
    }

    /**
     * Export report to PDF
     */
    public function exportPdf(Request $request)
    {
        $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $hasDateFilter = $request->has('start_date') || $request->has('end_date');

        $startDate = $request->start_date 
            ? Carbon::parse($request->start_date)->startOfDay() 
            : null;
        $endDate = $request->end_date 
            ? Carbon::parse($request->end_date)->endOfDay() 
            : null;

        // Get borrows
        $borrowsQuery = Borrow::with(['book', 'user', 'restore']);
        
        if ($hasDateFilter) {
            if ($startDate && $endDate) {
                $borrowsQuery->whereBetween('borrowed_at', [$startDate, $endDate]);
            } elseif ($startDate) {
                $borrowsQuery->where('borrowed_at', '>=', $startDate);
            } elseif ($endDate) {
                $borrowsQuery->where('borrowed_at', '<=', $endDate);
            }
        }
        
        $borrows = $borrowsQuery->latest('borrowed_at')->get();

        // Get returns
        $returnsQuery = Restore::with(['borrow.book', 'borrow.user']);
        
        if ($hasDateFilter) {
            if ($startDate && $endDate) {
                $returnsQuery->whereBetween('returned_at', [$startDate, $endDate]);
            } elseif ($startDate) {
                $returnsQuery->where('returned_at', '>=', $startDate);
            } elseif ($endDate) {
                $returnsQuery->where('returned_at', '<=', $endDate);
            }
        }
        
        $returns = $returnsQuery->latest('returned_at')->get();

        // Calculate summaries
        $summary = [
            'total_borrows' => $borrows->count(),
            'confirmed_borrows' => $borrows->where('confirmation', true)->count(),
            'pending_borrows' => $borrows->where('confirmation', false)->count(),
            'total_returns' => $returns->count(),
            'total_fines' => $returns->sum('fine'),
            'fines_paid' => $returns->where('is_paid', true)->sum('fine'),
            'fines_pending' => $returns->where('is_paid', false)->sum('fine'),
        ];

        $pdf = Pdf::loadView('admin.reports.pdf', [
            'borrows' => $borrows,
            'returns' => $returns,
            'summary' => $summary,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'hasDateFilter' => $hasDateFilter,
            'generatedAt' => now(),
        ]);

        $pdf->setPaper('A4', 'portrait');

        $filename = 'laporan-perpustakaan';
        if ($hasDateFilter) {
            if ($startDate) $filename .= '-' . $startDate->format('Y-m-d');
            if ($endDate) $filename .= '-' . $endDate->format('Y-m-d');
        } else {
            $filename .= '-semua';
        }
        $filename .= '.pdf';

        return $pdf->download($filename);
    }
}
