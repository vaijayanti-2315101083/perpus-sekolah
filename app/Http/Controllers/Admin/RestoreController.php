<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Restore;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class RestoreController extends Controller
{
    use LogsActivity;

    public function index(Request $request)
    {
        $restores = Restore::with(['book', 'user']);

        $restores->when($request->search, function (Builder $query) use ($request) {
            $query->where(function (Builder $q) use ($request) {
                $q->whereHas('book', function (Builder $query) use ($request) {
                    $query->where('title', 'LIKE', "%{$request->search}%");
                })
                ->orWhereHas('user', function (Builder $query) use ($request) {
                    $query->where('name', 'LIKE', "%{$request->search}%");
                });
            });
        });

        return view('admin.returns.index', [
            'restores' => $restores->latest()->paginate(10)
        ]);
    }

    public function edit($id)
    {
        return view('admin.returns.edit', [
            'restore' => Restore::with(['book', 'user', 'borrow'])->findOrFail($id)
        ]);
    }

    public function update(Request $request, $id)
    {
        $restore = Restore::with(['borrow', 'book', 'user'])->findOrFail($id);
        $oldValues = $restore->toArray();

        $dueDate = $restore->borrow->borrowed_at
            ->addDays($restore->borrow->duration);

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
            $restore->book->increment('amount', $restore->borrow->amount);
        }

        $restore->save();

        // Log activity
        $fineInfo = $fine > 0 ? " dengan denda Rp " . number_format($fine, 0, ',', '.') : " tanpa denda";
        $this->logUpdate($restore, $oldValues, "Memproses pengembalian buku '{$restore->book->title}' oleh {$restore->user->name}{$fineInfo}");

        $prefix = auth()->user()->role === 'Admin' ? 'admin' : 'pustakawan';
        return redirect()
            ->route("{$prefix}.returns.index")
            ->with('success', 'Pengembalian diproses otomatis.');
    }

    public function markAsPaid($id)
    {
        $restore = Restore::with(['borrow', 'book', 'user'])->findOrFail($id);
        $oldValues = $restore->toArray();

        // jika belum bayar
        if (!$restore->is_paid) {
            $restore->is_paid = true;
            $restore->status = Restore::STATUSES['Returned'];

            // stok buku balik
            $restore->book->increment('amount', $restore->borrow->amount);

            $restore->save();

            // Log activity
            $this->logUpdate($restore, $oldValues, "Menerima pembayaran denda Rp " . number_format($restore->fine, 0, ',', '.') . " dari {$restore->user->name} untuk buku '{$restore->book->title}'");
        }

        $prefix = auth()->user()->role === 'Admin' ? 'admin' : 'pustakawan';
        return redirect()
            ->route("{$prefix}.returns.index")
            ->with('success', 'Denda telah dibayar & buku dikembalikan.');
    }

    public function destroy($id)
    {
        $restore = Restore::with(['book', 'user'])->findOrFail($id);
        
        // Log before delete
        $this->logDelete($restore, "Menghapus data pengembalian buku '{$restore->book->title}' oleh {$restore->user->name}");

        $restore->delete();

        return back()->with('success', 'Data pengembalian dihapus.');
    }
}
