<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Borrow;
use App\Models\Book;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class BorrowApiController extends Controller
{
    /**
     * GET /api/borrows
     */
    public function index(Request $request)
    {
        $borrows = Borrow::with(['book', 'user']);

        // Filter by confirmation status
        if ($request->has('confirmed')) {
            $borrows->where('confirmation', $request->boolean('confirmed'));
        }

        // Filter by user
        if ($request->has('user_id')) {
            $borrows->where('user_id', $request->user_id);
        }

        // Search
        if ($request->has('search')) {
            $borrows->where(function (Builder $q) use ($request) {
                $q->whereHas('book', fn($q) => $q->where('title', 'LIKE', "%{$request->search}%"))
                  ->orWhereHas('user', fn($q) => $q->where('name', 'LIKE', "%{$request->search}%"));
            });
        }

        return response()->json([
            'data' => $borrows->latest('id')->paginate(15)
        ]);
    }

    /**
     * GET /api/borrows/{id}
     */
    public function show($id)
    {
        $borrow = Borrow::with(['book', 'user', 'restore'])->findOrFail($id);

        return response()->json([
            'data' => $borrow
        ]);
    }

    /**
     * POST /api/borrows
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'book_id' => ['required', 'exists:books,id'],
            'duration' => ['required', 'numeric', 'min:1', 'max:30'],
            'amount' => ['required', 'numeric', 'min:1'],
        ]);

        $book = Book::findOrFail($validated['book_id']);

        // Check stock
        if ($book->amount < $validated['amount']) {
            return response()->json([
                'message' => 'Stok buku tidak mencukupi',
                'available' => $book->amount
            ], 422);
        }

        $borrow = Borrow::create([
            'borrowed_at' => now(),
            'duration' => $validated['duration'],
            'amount' => $validated['amount'],
            'confirmation' => false,
            'book_id' => $book->id,
            'user_id' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Peminjaman berhasil diajukan',
            'data' => $borrow->load(['book', 'user'])
        ], 201);
    }

    /**
     * PATCH /api/borrows/{id}/confirm
     */
    public function confirm(Request $request, $id)
    {
        $borrow = Borrow::with('book')->findOrFail($id);

        if ($borrow->confirmation) {
            return response()->json([
                'message' => 'Peminjaman sudah dikonfirmasi sebelumnya'
            ], 422);
        }

        // Decrement stock
        $borrow->book()->decrement('amount', $borrow->amount);
        $borrow->update(['confirmation' => true]);

        return response()->json([
            'message' => 'Peminjaman dikonfirmasi',
            'data' => $borrow->fresh(['book', 'user'])
        ]);
    }

    /**
     * DELETE /api/borrows/{id}
     */
    public function destroy($id)
    {
        $borrow = Borrow::findOrFail($id);
        $borrow->delete();

        return response()->json([
            'message' => 'Peminjaman dihapus'
        ]);
    }
}
