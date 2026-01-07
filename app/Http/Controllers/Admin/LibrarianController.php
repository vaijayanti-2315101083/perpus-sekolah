<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class LibrarianController extends Controller
{
    use LogsActivity;

    public function index(Request $request)
    {
        $librarians = User::query()
            ->where('role', User::ROLES['Librarian']);

        $librarians->when($request->search, function (Builder $query) use ($request) {
            $query->where(function (Builder $q) use ($request) {
                $q->where('name', 'LIKE', "%{$request->search}%")
                    ->orWhere('number_type', 'LIKE', "%{$request->search}%")
                    ->orWhere('number', 'LIKE', "%{$request->search}%")
                    ->orWhere('telephone', 'LIKE', "%{$request->search}%");
            });
        });

        $librarians = $librarians->latest()->paginate(25);

        return view('admin.librarians.index')->with([
            'librarians' => $librarians,
        ]);
    }

    public function create()
    {
        return view('admin.librarians.create');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'number_type' => ['required', Rule::in(User::NUMBER_TYPES)],
            'number' => ['required', 'numeric', 'unique:' . User::class],
            'address' => ['required', 'string', 'max:255'],
            'telephone' => ['required', 'numeric'],
            'gender' => ['required', Rule::in(User::GENDERS)],
            'password' => ['required', 'string', 'confirmed', 'max:255'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        $credentials['role'] = User::ROLES['Librarian'];

        $password = $credentials['password'];
        $credentials['password'] = Hash::make($password);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $credentials['photo'] = $request->file('photo')->store('profile-photos', 'public');
        }

        $librarian = User::create($credentials);

        // Log activity
        $this->logCreate($librarian, "Menambahkan pustakawan baru: {$librarian->name} ({$librarian->number_type}: {$librarian->number})");

        return redirect()
            ->route('admin.librarians.index')
            ->with(
                'success',
                "Berhasil menambah pustakawan.
                <br />
                Nomor: {$credentials['number']}
                <br />
                Password: {$password}"
            );
    }

    public function edit($id)
    {
        $librarian = User::query()
            ->where('role', User::ROLES['Librarian'])
            ->findOrFail($id);

        return view('admin.librarians.edit')->with('librarian', $librarian);
    }

    public function update(Request $request, $id)
    {
        $librarian = User::query()
            ->where('role', User::ROLES['Librarian'])
            ->findOrFail($id);

        $oldValues = $librarian->toArray();

        $credentials = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'number_type' => ['required', Rule::in(User::NUMBER_TYPES)],
            'number' => ['required', 'numeric', Rule::unique(User::class)->ignore($librarian->id)],
            'address' => ['required', 'string', 'max:255'],
            'telephone' => ['required', 'numeric'],
            'gender' => ['required', Rule::in(User::GENDERS)],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'delete_photo' => ['nullable', 'boolean'],
        ]);

        $credentials['role'] = User::ROLES['Librarian'];

        $successMessage = "Berhasil mengedit pustakawan. <br /> Nomor: {$credentials['number']}";

        // Handle password update
        if (isset($request->password)) {
            $newPassword = $request->validate([
                'password' => ['required', 'string', 'confirmed', 'max:255'],
            ])['password'];

            $credentials['password'] = Hash::make($newPassword);
            $successMessage .= " <br /> Password Baru: {$newPassword}";
        }

        // Handle photo delete
        if ($request->delete_photo && $librarian->photo) {
            Storage::disk('public')->delete($librarian->photo);
            $credentials['photo'] = null;
        }

        // Handle photo upload
        if ($request->hasFile('photo')) {
            if ($librarian->photo) {
                Storage::disk('public')->delete($librarian->photo);
            }
            $credentials['photo'] = $request->file('photo')->store('profile-photos', 'public');
        }

        $librarian->update($credentials);

        // Log activity
        $this->logUpdate($librarian, $oldValues, "Mengubah data pustakawan: {$librarian->name}");

        return redirect()
            ->route('admin.librarians.index')
            ->with('success', $successMessage);
    }

    public function destroy($id)
    {
        $librarian = User::query()
            ->where('role', User::ROLES['Librarian'])
            ->findOrFail($id);

        // Log before delete
        $this->logDelete($librarian, "Menghapus pustakawan: {$librarian->name} ({$librarian->number_type}: {$librarian->number})");

        // Delete photo when deleting user
        if ($librarian->photo) {
            Storage::disk('public')->delete($librarian->photo);
        }

        $librarian->delete();

        return redirect()
            ->route('admin.librarians.index')
            ->with('success', 'Berhasil menghapus pustakawan.');
    }
}