<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Borrow;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BorrowController extends Controller
{
    use LogsActivity;

    public function index(Request $request)
    {
        $borrows = Borrow::with(['book', 'user']);

        $borrows->when($request->search, function (Builder $query) use ($request) {
            $query->where(function (Builder $q) use ($request) {
                $q->whereHas('book', function (Builder $query) use ($request) {
                    $query->where('title', 'LIKE', "%{$request->search}%");
                })
                    ->orWhereHas('user', function (Builder $query) use ($request) {
                        $query->where('name', 'LIKE', "%{$request->search}%");
                    });
            });
        });

        $borrows = $borrows->latest('id')->paginate(10);

        return view('admin.borrows.index')->with([
            'borrows' => $borrows,
        ]);
    }

    public function edit(Borrow $borrow)
    {
        return view('admin.borrows.edit')->with([
            'borrow' => $borrow,
        ]);
    }

    public function update(Request $request, Borrow $borrow)
    {
        $data = $request->validate([
            'confirmation' => ['required', Rule::in([1])],
        ]);

        $oldValues = $borrow->toArray();

        // jika peminjaman belum terkonfirmasi kemudian saat ini dikonfirmasi
        if (!$borrow->confirmation) {
            $borrow->book()->decrement('amount', $borrow->amount);
        }

        if ($borrow->fine > 0 && !$borrow->is_paid) {
            return back()->with('error', 'Harap bayar denda terlebih dahulu');
        }

        $borrow->update($data);

        // Log activity
        $this->logUpdate($borrow, $oldValues, "Mengkonfirmasi peminjaman buku '{$borrow->book->title}' oleh {$borrow->user->name}");

        return redirect()
            ->route('admin.borrows.index')
            ->with('success', 'Berhasil mengubah status konfirmasi peminjaman.');
    }

    public function destroy(Borrow $borrow)
    {
        $bookTitle = $borrow->book->title ?? 'Unknown';
        $userName = $borrow->user->name ?? 'Unknown';

        // Log before delete
        $this->logDelete($borrow, "Menghapus peminjaman buku '{$bookTitle}' oleh {$userName}");

        $borrow->delete();

        return redirect()
            ->route('admin.borrows.index')
            ->with('success', 'Berhasil menghapus peminjaman.');
    }
}
