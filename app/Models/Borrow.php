<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Borrow extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'borrowed_at',
        'duration',
        'amount',
        'confirmation',
        'book_id',
        'user_id',
        'fine',
        'is_paid',
        'virtual_account',
        'returned_at',
    ];

    protected $casts = [
        'borrowed_at' => 'datetime',
    ];

    // Status keys (English - untuk internal/comparison)
    public const STATUSES = [
        'Pending' => 'Pending',
        'Borrowed' => 'Borrowed',
        'Returning' => 'Returning',
        'Returned' => 'Returned',
        'Overdue' => 'Overdue',
    ];

    // Labels untuk display (Indonesian)
    public const STATUS_LABELS = [
        'Pending' => 'Menunggu Konfirmasi',
        'Borrowed' => 'Sedang Dipinjam',
        'Returning' => 'Dalam Pengembalian',
        'Returned' => 'Dikembalikan',
        'Overdue' => 'Terlambat',
    ];

    // Accessor untuk mendapatkan status (computed)
    public function getStatusAttribute(): string
    {
        // Belum dikonfirmasi
        if (!$this->confirmation) {
            return self::STATUSES['Pending'];
        }

        // Sudah ada return
        if ($this->restore) {
            // Return sudah selesai (dikonfirmasi/dibayar)
            if ($this->restore->status === \App\Models\Restore::STATUSES['Returned']) {
                return self::STATUSES['Returned'];
            }
            // Return masih dalam proses
            return self::STATUSES['Returning'];
        }

        // Sedang dipinjam - cek apakah terlambat
        if ($this->dueDate() < now()) {
            return self::STATUSES['Overdue'];
        }

        return self::STATUSES['Borrowed'];
    }

    // Accessor untuk mendapatkan label Indonesia
    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    // ================= RELATIONS =================
    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function restore()
    {
        return $this->hasOne(Restore::class);
    }

    // ================= LOGIC =================
    public function dueDate()
    {
        return $this->borrowed_at->copy()->addDays($this->duration);
    }

    public function calculateFine()
    {
        if (!$this->restore || !$this->restore->returned_at) {
            return 0;
        }

        $lateDays = max(
            $this->restore->returned_at->diffInDays($this->dueDate(), false),
            0
        );

        return $lateDays * 5000;
    }
}
