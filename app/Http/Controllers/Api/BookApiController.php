<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BookApiController extends Controller
{
    /**
     * GET /api/books
     */
    public function index(Request $request)
    {
        $books = Book::query();

        // Filter by status
        if ($request->has('status')) {
            $books->where('status', $request->status);
        }

        // Filter by category
        if ($request->has('category')) {
            $books->where('category', $request->category);
        }

        // Search
        if ($request->has('search')) {
            $books->where(function (Builder $q) use ($request) {
                $q->where('title', 'LIKE', "%{$request->search}%")
                  ->orWhere('writer', 'LIKE', "%{$request->search}%")
                  ->orWhere('publisher', 'LIKE', "%{$request->search}%");
            });
        }

        return response()->json([
            'data' => $books->latest('id')->paginate(15)
        ]);
    }

    /**
     * GET /api/books/{id}
     */
    public function show($id)
    {
        $book = Book::with('borrows')->findOrFail($id);

        return response()->json([
            'data' => $book
        ]);
    }

    /**
     * POST /api/books
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'synopsis' => ['nullable', 'string'],
            'publisher' => ['required', 'string', 'max:255'],
            'writer' => ['required', 'string', 'max:255'],
            'publish_year' => ['required', 'numeric'],
            'category' => ['nullable', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'status' => ['nullable', Rule::in(Book::STATUSES)],
        ]);

        $validated['status'] = $validated['status'] ?? 'Available';
        
        $book = Book::create($validated);

        return response()->json([
            'message' => 'Buku berhasil ditambahkan',
            'data' => $book
        ], 201);
    }

    /**
     * PUT /api/books/{id}
     */
    public function update(Request $request, $id)
    {
        $book = Book::findOrFail($id);

        $validated = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'synopsis' => ['sometimes', 'string'],
            'publisher' => ['sometimes', 'string', 'max:255'],
            'writer' => ['sometimes', 'string', 'max:255'],
            'publish_year' => ['sometimes', 'numeric'],
            'category' => ['sometimes', 'string', 'max:255'],
            'amount' => ['sometimes', 'numeric', 'min:0'],
            'status' => ['sometimes', Rule::in(Book::STATUSES)],
        ]);

        $book->update($validated);

        return response()->json([
            'message' => 'Buku berhasil diupdate',
            'data' => $book->fresh()
        ]);
    }

    /**
     * DELETE /api/books/{id}
     */
    public function destroy($id)
    {
        Book::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Buku berhasil dihapus'
        ]);
    }
}
