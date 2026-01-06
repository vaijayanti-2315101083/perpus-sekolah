<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;

class BookApiController extends Controller
{
    // GET /api/books
    public function index()
    {
        return response()->json(Book::all(), 200);
    }

    // POST /api/books
    public function store(Request $request)
    {
        $book = Book::create([
            'title' => $request->title,
            'synopsis' => $request->synopsis,
            'publisher' => $request->publisher,
            'writer' => $request->writer,
            'publish_year' => $request->publish_year,
            'category' => $request->category,
            'amount' => $request->amount,
            'status' => 'Tersedia',
            'cover' => null
        ]);

        return response()->json([
            'message' => 'Book created',
            'data' => $book
        ], 201);
    }

    // DELETE /api/books/{id}
    public function destroy($id)
    {
        Book::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Book deleted'
        ], 200);
    }
}
