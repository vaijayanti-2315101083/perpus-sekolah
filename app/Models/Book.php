<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Borrow;

class Book extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'synopsis',
        'writer',
        'publisher',
        'publish_year',
        'cover',
        'category',
        'amount',
        'status',
    ];

    // STATUS BUKU
    public const STATUSES = [
        'Available' => 'Available',
        'Unavailable' => 'Unavailable',
    ];

    // RELASI KE BORROWS
    public function borrows()
    {
        return $this->hasMany(Borrow::class);
    }
}
