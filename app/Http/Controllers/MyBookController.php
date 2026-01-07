<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Book;
use App\Models\Borrow;
use App\Models\Restore;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class MyBookController extends Controller
{
    /**
     * Display a listing of user's borrowed books.
     */
    public function index()
    {
        // Only Members can access my-books
        // Admin/Pustakawan should go to their dashboard
        if (auth()->user()->role !== 'Member') {
            return redirect(dashboard_route());
        }

        $currentBorrows = Borrow::query()
            ->with('book')
            ->whereBelongsTo(auth()->user())
            ->whereDoesntHave('restore', function (Builder $query) {
                $query->where('confirmation', true);
            })
            ->latest('id')
            ->paginate(6);

        $recentBorrows = Borrow::query()
            ->with(['book', 'restore'])
            ->whereBelongsTo(auth()->user())
            ->whereHas('restore', function (Builder $query) {
                $query->where('confirmation', true);
            })
            ->latest('id')
            ->limit(6)
            ->get();

        return view('my-books', [
            'currentBorrows' => $currentBorrows,
            'recentBorrows' => $recentBorrows,
        ]);
    }

    /**
     * Store a new borrow request.
     */
    public function store(Request $request, Book $book)
    {
        // Only Members can borrow books
        if (auth()->user()->role !== 'Member') {
            return redirect(dashboard_route());
        }

        $request->validate([
            'duration' => ['required', 'numeric'],
            'amount' => ['required', 'numeric', 'max:' . $book->amount],
        ]);

        $borrow = Borrow::create([
            'borrowed_at' => now(),
            'duration' => $request->duration,
            'amount' => $request->amount,
            'confirmation' => false,
            'book_id' => $book->id,
            'user_id' => auth()->id(),
        ]);

        // Log activity
        ActivityLog::log('create', "Mengajukan peminjaman buku '{$book->title}' (jumlah: {$request->amount}, durasi: {$request->duration} hari)", $borrow);

        return redirect()->route('my-books.index')->with('success', 'Berhasil mengajukan peminjaman!');
    }

    /**
     * Update (return) a borrowed book.
     */
    public function update($id)
    {
        // Only Members can return books
        if (auth()->user()->role !== 'Member') {
            return redirect(dashboard_route());
        }

        $borrow = Borrow::query()->with('book')->findOrFail($id);

        if (!$borrow->confirmation || isset($borrow->restore)) {
            return back()->withErrors(['default' => 'Peminjaman ini tidak sesuai!']);
        }

        $returnStatus = $borrow->borrowed_at->addDays($borrow->duration) > now() 
            ? Restore::STATUSES['Not confirmed'] 
            : Restore::STATUSES['Past due'];

        $restore = Restore::create([
            'returned_at' => now(),
            'status' => $returnStatus,
            'confirmation' => 0,
            'book_id' => $borrow->book->id,
            'user_id' => auth()->id(),
            'borrow_id' => $borrow->id,
        ]);

        // Log activity
        $statusLabel = $returnStatus === Restore::STATUSES['Past due'] ? ' (terlambat)' : '';
        ActivityLog::log('create', "Mengajukan pengembalian buku '{$borrow->book->title}'{$statusLabel}", $restore);

        return redirect()->route('my-books.index')->with('success', 'Berhasil mengajukan pengembalian!');
    }
}