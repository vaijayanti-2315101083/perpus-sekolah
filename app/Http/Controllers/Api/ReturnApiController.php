<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Borrow;
use App\Models\Restore;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ReturnApiController extends Controller
{
    /**
     * GET /api/returns
     */
    public function index(Request $request)
    {
        $returns = Restore::with(['borrow.book', 'borrow.user']);

        // Filter by status
        if ($request->has('status')) {
            $returns->where('status', $request->status);
        }

        // Filter by paid status
        if ($request->has('is_paid')) {
            $returns->where('is_paid', $request->boolean('is_paid'));
        }

        // Search
        if ($request->has('search')) {
            $returns->whereHas('borrow', function (Builder $q) use ($request) {
                $q->whereHas('book', fn($q) => $q->where('title', 'LIKE', "%{$request->search}%"))
                  ->orWhereHas('user', fn($q) => $q->where('name', 'LIKE', "%{$request->search}%"));
            });
        }

        return response()->json([
            'data' => $returns->latest()->paginate(15)
        ]);
    }

    /**
     * GET /api/returns/{id}
     */
    public function show($id)
    {
        $restore = Restore::with(['borrow.book', 'borrow.user'])->findOrFail($id);

        return response()->json([
            'data' => $restore
        ]);
    }

    /**
     * POST /api/returns
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'borrow_id' => ['required', 'exists:borrows,id', 'unique:returns,borrow_id'],
        ]);

        $borrow = Borrow::with('book')->findOrFail($validated['borrow_id']);

        // Check if borrow is confirmed
        if (!$borrow->confirmation) {
            return response()->json([
                'message' => 'Peminjaman belum dikonfirmasi'
            ], 422);
        }

        // Calculate status
        $returnStatus = $borrow->borrowed_at->addDays($borrow->duration) > now()
            ? Restore::STATUSES['Not confirmed']
            : Restore::STATUSES['Past due'];

        $restore = Restore::create([
            'borrow_id' => $borrow->id,
            'book_id' => $borrow->book->id,
            'user_id' => $request->user()->id,
            'returned_at' => now(),
            'status' => $returnStatus,
            'confirmation' => false,
        ]);

        return response()->json([
            'message' => 'Pengajuan pengembalian berhasil',
            'data' => $restore->load(['borrow.book', 'borrow.user'])
        ], 201);
    }

    /**
     * PATCH /api/returns/{id}/process
     */
    public function process($id)
    {
        $restore = Restore::with(['borrow.book', 'borrow'])->findOrFail($id);

        $dueDate = $restore->borrow->borrowed_at->addDays($restore->borrow->duration);
        $lateDays = max($dueDate->diffInDays(now(), false), 0);
        $fine = $lateDays * 5000;

        $restore->returned_at = now();
        $restore->fine = $fine;

        if ($fine > 0) {
            $restore->status = Restore::STATUSES['Fine not paid'];
            $restore->virtual_account = 'VA-' . rand(10000000, 99999999);
            $restore->is_paid = false;
        } else {
            $restore->status = Restore::STATUSES['Returned'];
            $restore->is_paid = true;
            $restore->borrow->book->increment('amount', $restore->borrow->amount);
        }

        $restore->save();

        return response()->json([
            'message' => 'Pengembalian diproses',
            'fine' => $fine,
            'virtual_account' => $restore->virtual_account,
            'data' => $restore->fresh(['borrow.book', 'borrow.user'])
        ]);
    }

    /**
     * PATCH /api/returns/{id}/pay
     */
    public function pay($id)
    {
        $restore = Restore::with(['borrow.book', 'borrow'])->findOrFail($id);

        if ($restore->is_paid) {
            return response()->json([
                'message' => 'Denda sudah dibayar'
            ], 422);
        }

        $restore->is_paid = true;
        $restore->status = Restore::STATUSES['Returned'];
        $restore->borrow->book->increment('amount', $restore->borrow->amount);
        $restore->save();

        return response()->json([
            'message' => 'Pembayaran denda berhasil, buku dikembalikan',
            'data' => $restore->fresh(['borrow.book', 'borrow.user'])
        ]);
    }

    /**
     * DELETE /api/returns/{id}
     */
    public function destroy($id)
    {
        Restore::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Data pengembalian dihapus'
        ]);
    }
}
