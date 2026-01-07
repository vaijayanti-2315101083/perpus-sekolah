<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restore extends Model
{
    use HasFactory;
    
    protected $table = 'returns'; // 🔥 INI KUNCI UTAMANYA
    public $timestamps = false; // Tabel returns tidak punya created_at/updated_at

    protected $fillable = [
        'borrow_id',
        'book_id',
        'user_id',
        'returned_at',
        'fine',
        'status',
        'confirmation',
        'virtual_account',
        'is_paid',
    ];

    protected $casts = [
        'returned_at' => 'datetime',
        'is_paid' => 'boolean',
    ];

    // Keys untuk database (English - untuk switch/comparison)
    public const STATUSES = [
        'Not confirmed' => 'Not confirmed',
        'Past due' => 'Past due',
        'Fine not paid' => 'Fine not paid',
        'Returned' => 'Returned',
    ];

    // Labels untuk display (Indonesian)
    public const STATUS_LABELS = [
        'Not confirmed' => 'Belum Dikonfirmasi',
        'Past due' => 'Terlambat',
        'Fine not paid' => 'Denda Belum Dibayar',
        'Returned' => 'Dikembalikan',
    ];

    // Accessor untuk mendapatkan label Indonesia
    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function borrow()
    {
        return $this->belongsTo(Borrow::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
