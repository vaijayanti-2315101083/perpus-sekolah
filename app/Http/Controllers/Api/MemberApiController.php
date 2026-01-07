<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class MemberApiController extends Controller
{
    /**
     * GET /api/members
     */
    public function index(Request $request)
    {
        $members = User::where('role', 'Member');

        // Search
        if ($request->has('search')) {
            $members->where(function (Builder $q) use ($request) {
                $q->where('name', 'LIKE', "%{$request->search}%")
                  ->orWhere('number', 'LIKE', "%{$request->search}%");
            });
        }

        return response()->json([
            'data' => $members->latest()->paginate(15)
        ]);
    }

    /**
     * GET /api/members/{id}
     */
    public function show($id)
    {
        $member = User::where('role', 'Member')->findOrFail($id);

        return response()->json([
            'data' => $member->load('borrows')
        ]);
    }

    /**
     * POST /api/members
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'number_type' => ['required', Rule::in(User::NUMBER_TYPES)],
            'number' => ['required', 'numeric', 'unique:users'],
            'address' => ['required', 'string', 'max:255'],
            'telephone' => ['required', 'numeric'],
            'gender' => ['required', Rule::in(['Man', 'Woman'])],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $validated['role'] = 'Member';
        $validated['password'] = Hash::make($validated['password']);

        $member = User::create($validated);

        return response()->json([
            'message' => 'Member berhasil ditambahkan',
            'data' => $member
        ], 201);
    }

    /**
     * PUT /api/members/{id}
     */
    public function update(Request $request, $id)
    {
        $member = User::where('role', 'Member')->findOrFail($id);

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'number_type' => ['sometimes', Rule::in(User::NUMBER_TYPES)],
            'number' => ['sometimes', 'numeric', Rule::unique('users')->ignore($member->id)],
            'address' => ['sometimes', 'string', 'max:255'],
            'telephone' => ['sometimes', 'numeric'],
            'gender' => ['sometimes', Rule::in(['Man', 'Woman'])],
            'password' => ['sometimes', 'string', 'min:6'],
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $member->update($validated);

        return response()->json([
            'message' => 'Member berhasil diupdate',
            'data' => $member->fresh()
        ]);
    }

    /**
     * DELETE /api/members/{id}
     */
    public function destroy($id)
    {
        $member = User::where('role', 'Member')->findOrFail($id);
        $member->delete();

        return response()->json([
            'message' => 'Member berhasil dihapus'
        ]);
    }
}
