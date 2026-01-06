<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeProfileFeature extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:profile-feature 
                            {--force : Overwrite existing files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate complete profile feature (Controller, Views, Migration, Routes)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Generating Profile Feature...');
        $this->newLine();

        $force = $this->option('force');

        // Create directories if not exist
        $this->createDirectories();

        // Generate files
        $this->generateProfileController($force);
        $this->generateAdminProfileView($force);
        $this->generateMemberProfileView($force);
        $this->generatePhotoMigration($force);
        $this->updateUserModel();
        $this->generateRoutesFile();

        $this->newLine();
        $this->info('✅ Profile feature generated successfully!');
        $this->newLine();

        // Show next steps
        $this->displayNextSteps();

        return Command::SUCCESS;
    }

    /**
     * Create necessary directories
     */
    protected function createDirectories()
    {
        $directories = [
            app_path('Http/Controllers/Admin'),
            resource_path('views/admin/profile'),
        ];

        foreach ($directories as $directory) {
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
                $this->line("📁 Created directory: {$directory}");
            }
        }
    }

    /**
     * Generate ProfileController
     */
    protected function generateProfileController($force)
    {
        $controllerPath = app_path('Http/Controllers/Admin/ProfileController.php');

        if (File::exists($controllerPath) && !$force) {
            $this->warn('⚠️  ProfileController already exists. Use --force to overwrite.');
            return;
        }

        $stub = $this->getProfileControllerStub();
        File::put($controllerPath, $stub);
        $this->info('✅ ProfileController created: ' . $controllerPath);
    }

    /**
     * Generate admin profile view
     */
    protected function generateAdminProfileView($force)
    {
        $viewPath = resource_path('views/admin/profile/index.blade.php');

        if (File::exists($viewPath) && !$force) {
            $this->warn('⚠️  Admin profile view already exists. Use --force to overwrite.');
            return;
        }

        $stub = $this->getAdminProfileViewStub();
        File::put($viewPath, $stub);
        $this->info('✅ Admin profile view created: ' . $viewPath);
    }

    /**
     * Generate member profile view
     */
    protected function generateMemberProfileView($force)
    {
        $viewPath = resource_path('views/profile.blade.php');

        if (File::exists($viewPath) && !$force) {
            $this->warn('⚠️  Member profile view already exists. Use --force to overwrite.');
            return;
        }

        $stub = $this->getMemberProfileViewStub();
        File::put($viewPath, $stub);
        $this->info('✅ Member profile view created: ' . $viewPath);
    }

    /**
     * Generate photo migration
     */
    protected function generatePhotoMigration($force)
    {
        $timestamp = date('Y_m_d_His');
        $migrationPath = database_path("migrations/{$timestamp}_add_photo_to_users_table.php");

        // Check if migration already exists
        $existingMigrations = glob(database_path('migrations/*_add_photo_to_users_table.php'));
        if (!empty($existingMigrations) && !$force) {
            $this->warn('⚠️  Photo migration already exists. Use --force to overwrite.');
            return;
        }

        $stub = $this->getPhotoMigrationStub();
        File::put($migrationPath, $stub);
        $this->info('✅ Migration created: ' . $migrationPath);
    }

    /**
     * Update User model
     */
    protected function updateUserModel()
    {
        $modelPath = app_path('Models/User.php');

        if (!File::exists($modelPath)) {
            $this->error('❌ User model not found at: ' . $modelPath);
            return;
        }

        $content = File::get($modelPath);

        // Check if photo is already in fillable
        if (strpos($content, "'photo'") !== false) {
            $this->warn('⚠️  Photo column already exists in User model fillable.');
            return;
        }

        // Add photo to fillable array
        $content = preg_replace(
            "/(protected \\\$fillable = \[.*?)'gender',/s",
            "$1'gender',\n        'photo',",
            $content
        );

        File::put($modelPath, $content);
        $this->info('✅ User model updated: Added photo to fillable');
    }

    /**
     * Generate routes file
     */
    protected function generateRoutesFile()
    {
        $routesPath = base_path('routes/profile_routes.php');

        $stub = $this->getRoutesStub();
        File::put($routesPath, $stub);
        $this->info('✅ Routes file created: ' . $routesPath);
    }

    /**
     * Display next steps
     */
    protected function displayNextSteps()
    {
        $this->line('📋 <fg=yellow>NEXT STEPS:</>');
        $this->newLine();

        $this->line('1️⃣  Run migration:');
        $this->line('   <fg=green>php artisan migrate</>');
        $this->newLine();

        $this->line('2️⃣  Add routes to routes/web.php:');
        $this->line('   <fg=cyan>require __DIR__.\'/profile_routes.php\';</> (at the end of file)');
        $this->newLine();

        $this->line('3️⃣  Create storage link (if not exists):');
        $this->line('   <fg=green>php artisan storage:link</>');
        $this->newLine();

        $this->line('4️⃣  Update topbar.blade.php to add profile menu:');
        $this->line('   Add this before logout link in dropdown:');
        $this->line('   <fg=cyan><a class="dropdown-item" href="{{ route(\'admin.profile.index\') }}">');
        $this->line('       <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>');
        $this->line('       Profil');
        $this->line('   </a></>');
        $this->newLine();

        $this->line('5️⃣  Update navigation.blade.php to add profile menu for members:');
        $this->line('   Add this in navbar after "Buku-ku":');
        $this->line('   <fg=cyan><a class="nav-link" href="{{ route(\'profile.index\') }}">Profil</a></>');
        $this->newLine();

        $this->line('✨ <fg=green>All done! Test your profile feature.</>');
    }

    /**
     * Get ProfileController stub
     */
    protected function getProfileControllerStub()
    {
        return <<<'PHP'
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Display user profile
     */
    public function index()
    {
        $user = Auth::user();
        
        // Determine which view to use based on user role
        if (in_array($user->role, ['Admin', 'Pustakawan'])) {
            return view('admin.profile.index')->with('user', $user);
        }
        
        return view('profile')->with('user', $user);
    }

    /**
     * Update user profile data
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'number_type' => ['required', Rule::in(User::NUMBER_TYPES)],
            'number' => ['required', 'numeric', Rule::unique(User::class)->ignore($user->id)],
            'address' => ['required', 'string', 'max:255'],
            'telephone' => ['required', 'numeric'],
            'gender' => ['required', Rule::in(User::GENDERS)],
        ]);

        $user->update($validated);

        $route = in_array($user->role, ['Admin', 'Pustakawan']) 
            ? 'admin.profile.index' 
            : 'profile.index';

        return redirect()
            ->route($route)
            ->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Update user password
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'confirmed', 'min:8'],
        ]);

        // Check if current password is correct
        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors([
                'current_password' => 'Password saat ini tidak sesuai.'
            ]);
        }

        // Update password
        $user->update([
            'password' => Hash::make($validated['password'])
        ]);

        $route = in_array($user->role, ['Admin', 'Pustakawan']) 
            ? 'admin.profile.index' 
            : 'profile.index';

        return redirect()
            ->route($route)
            ->with('success', 'Password berhasil diperbarui.');
    }

    /**
     * Update user photo
     */
    public function updatePhoto(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'photo' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        // Delete old photo if exists
        if ($user->photo) {
            Storage::disk('public')->delete($user->photo);
        }

        // Store new photo
        $photoPath = $request->file('photo')->store('photos', 'public');

        $user->update([
            'photo' => $photoPath
        ]);

        $route = in_array($user->role, ['Admin', 'Pustakawan']) 
            ? 'admin.profile.index' 
            : 'profile.index';

        return redirect()
            ->route($route)
            ->with('success', 'Foto profil berhasil diperbarui.');
    }

    /**
     * Delete user photo
     */
    public function deletePhoto()
    {
        $user = Auth::user();

        if ($user->photo) {
            Storage::disk('public')->delete($user->photo);
            
            $user->update([
                'photo' => null
            ]);

            $route = in_array($user->role, ['Admin', 'Pustakawan']) 
                ? 'admin.profile.index' 
                : 'profile.index';

            return redirect()
                ->route($route)
                ->with('success', 'Foto profil berhasil dihapus.');
        }

        $route = in_array($user->role, ['Admin', 'Pustakawan']) 
            ? 'admin.profile.index' 
            : 'profile.index';

        return redirect()
            ->route($route)
            ->with('error', 'Tidak ada foto profil untuk dihapus.');
    }
}
PHP;
    }

    /**
     * Get admin profile view stub
     */
    protected function getAdminProfileViewStub()
    {
        return <<<'BLADE'
<x-admin-layout title="Profil Saya">
    {{-- Success Message --}}
    @if ($success = session()->get('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle mr-2"></i>{{ $success }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if ($error = session()->get('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-exclamation-circle mr-2"></i>{{ $error }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        {{-- Profile Photo & Info Card --}}
        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="card shadow">
                <div class="card-body text-center">
                    <div class="mb-4">
                        @if(isset($user->photo))
                            <img src="{{ asset('storage/' . $user->photo) }}" 
                                 alt="Profile Photo" 
                                 class="rounded-circle shadow-sm"
                                 id="profilePhotoPreview"
                                 style="width: 150px; height: 150px; object-fit: cover;">
                        @else
                            <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center shadow-sm"
                                 style="width: 150px; height: 150px; font-size: 4rem;">
                                <i class="fas fa-user"></i>
                            </div>
                        @endif
                    </div>

                    <h4 class="font-weight-bold mb-1">{{ $user->name }}</h4>
                    <p class="text-muted mb-3">
                        <span class="badge badge-primary">{{ $user->role }}</span>
                    </p>

                    <div class="text-left mt-4">
                        <div class="mb-2">
                            <small class="text-muted">Nomor:</small>
                            <p class="mb-0 font-weight-bold">{{ $user->number_type }} - {{ $user->number }}</p>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">Telepon:</small>
                            <p class="mb-0 font-weight-bold">+{{ $user->telephone }}</p>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">Jenis Kelamin:</small>
                            <p class="mb-0 font-weight-bold">{{ $user->gender }}</p>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">Alamat:</small>
                            <p class="mb-0 font-weight-bold">{{ $user->address }}</p>
                        </div>
                    </div>

                    {{-- Upload/Delete Photo Form --}}
                    <div class="mt-4 pt-4 border-top">
                        <h6 class="font-weight-bold mb-3">Foto Profil</h6>
                        
                        <form action="{{ route('admin.profile.photo.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <div class="form-group">
                                <div class="custom-file">
                                    <input type="file" 
                                           name="photo" 
                                           class="custom-file-input @error('photo') is-invalid @enderror" 
                                           id="photoInput"
                                           accept="image/jpeg,image/png,image/jpg"
                                           onchange="previewPhoto(event)">
                                    <label class="custom-file-label" for="photoInput">Pilih foto...</label>
                                </div>
                                @error('photo')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                                <small class="form-text text-muted">Format: JPG, PNG. Max: 2MB</small>
                            </div>

                            <button type="submit" class="btn btn-primary btn-sm btn-block">
                                <i class="fas fa-upload mr-1"></i>Upload Foto
                            </button>
                        </form>

                        @if(isset($user->photo))
                            <form action="{{ route('admin.profile.photo.delete') }}" method="POST" class="mt-2"
                                  onsubmit="return confirm('Yakin ingin menghapus foto profil?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm btn-block">
                                    <i class="fas fa-trash mr-1"></i>Hapus Foto
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8 col-lg-7">
            {{-- Edit Profile Card --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user-edit mr-2"></i>Edit Profil
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="name" class="form-label font-weight-bold">Nama Lengkap</label>
                                <input type="text" 
                                       name="name" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       value="{{ old('name', $user->name) }}">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="number_type" class="form-label font-weight-bold">Tipe Nomor</label>
                                <select name="number_type" 
                                        id="number_type" 
                                        class="form-control @error('number_type') is-invalid @enderror">
                                    @foreach (\App\Models\User::NUMBER_TYPES as $numberType)
                                        <option @selected(old('number_type', $user->number_type) === $numberType) 
                                                value="{{ $numberType }}">
                                            {{ $numberType }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('number_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="number" class="form-label font-weight-bold">Nomor</label>
                                <input type="number" 
                                       name="number" 
                                       class="form-control @error('number') is-invalid @enderror" 
                                       id="number"
                                       value="{{ old('number', $user->number) }}">
                                @error('number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="address" class="form-label font-weight-bold">Alamat</label>
                                <textarea name="address" 
                                          class="form-control @error('address') is-invalid @enderror" 
                                          id="address" 
                                          rows="2">{{ old('address', $user->address) }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="telephone" class="form-label font-weight-bold">Telepon</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">+</span>
                                    </div>
                                    <input type="number" 
                                           name="telephone" 
                                           id="telephone" 
                                           class="form-control @error('telephone') is-invalid @enderror"
                                           value="{{ old('telephone', $user->telephone) }}">
                                    @error('telephone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="form-text text-muted">Contoh: 6281234567890</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold d-block">Jenis Kelamin</label>
                                <div class="mt-2">
                                    @foreach (\App\Models\User::GENDERS as $gender)
                                        <div class="form-check form-check-inline">
                                            <input @checked(old('gender', $user->gender) === $gender) 
                                                   class="form-check-input @error('gender') is-invalid @enderror" 
                                                   type="radio" 
                                                   name="gender"
                                                   id="{{ $gender }}" 
                                                   value="{{ $gender }}">
                                            <label class="form-check-label" for="{{ $gender }}">
                                                {{ $gender }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                @error('gender')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save mr-2"></i>Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Change Password Card --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-key mr-2"></i>Ubah Password
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.profile.password.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="current_password" class="form-label font-weight-bold">
                                    Password Saat Ini
                                </label>
                                <input type="password" 
                                       name="current_password" 
                                       class="form-control @error('current_password') is-invalid @enderror" 
                                       id="current_password">
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label font-weight-bold">
                                    Password Baru
                                </label>
                                <input type="password" 
                                       name="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       id="password">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Minimal 8 karakter</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label font-weight-bold">
                                    Konfirmasi Password Baru
                                </label>
                                <input type="password" 
                                       name="password_confirmation" 
                                       class="form-control" 
                                       id="password_confirmation">
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-warning px-4">
                                <i class="fas fa-lock mr-2"></i>Ubah Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @section('scripts')
        <script>
            function previewPhoto(event) {
                const file = event.target.files[0];
                const fileName = file ? file.name : 'Pilih foto...';
                event.target.nextElementSibling.textContent = fileName;
                
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const preview = document.getElementById('profilePhotoPreview');
                        if (preview) {
                            preview.src = e.target.result;
                        }
                    };
                    reader.readAsDataURL(file);
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(function() {
                    const alerts = document.querySelectorAll('.alert');
                    alerts.forEach(function(alert) {
                        const bsAlert = new bootstrap.Alert(alert);
                        bsAlert.close();
                    });
                }, 5000);
            });
        </script>
    @endsection
</x-admin-layout>
BLADE;
    }

    /**
     * Get member profile view stub
     */
    protected function getMemberProfileViewStub()
    {
        return <<<'BLADE'
<x-app-layout>
    <!-- Spacer for fixed navbar -->
    <div style="height: 56px;"></div>
    @auth
        @if (in_array(auth()->user()->role, [\App\Models\User::ROLES['Admin'], \App\Models\User::ROLES['Librarian']]))
            <div style="height: 48px;"></div>
        @endif
    @endauth

    <section class="container py-5" style="min-height: 100vh;">
        {{-- Success/Error Messages --}}
        @if ($success = session()->get('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ $success }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($error = session()->get('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ $error }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row g-4">
            {{-- Profile Card --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="mb-4">
                            @if(isset($user->photo))
                                <img src="{{ asset('storage/' . $user->photo) }}" 
                                     alt="Profile Photo" 
                                     class="rounded-circle shadow"
                                     id="profilePhotoPreview"
                                     style="width: 150px; height: 150px; object-fit: cover;">
                            @else
                                <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center shadow"
                                     style="width: 150px; height: 150px; font-size: 4rem;">
                                    <i class="bi bi-person-fill"></i>
                                </div>
                            @endif
                        </div>

                        <h4 class="fw-bold mb-1">{{ $user->name }}</h4>
                        <p class="text-muted mb-3">
                            <span class="badge bg-primary">{{ $user->role }}</span>
                        </p>

                        <div class="text-start mt-4">
                            <div class="mb-3 pb-3 border-bottom">
                                <small class="text-muted d-block mb-1">Nomor</small>
                                <p class="mb-0 fw-semibold">{{ $user->number_type }} - {{ $user->number }}</p>
                            </div>
                            <div class="mb-3 pb-3 border-bottom">
                                <small class="text-muted d-block mb-1">Telepon</small>
                                <p class="mb-0 fw-semibold">+{{ $user->telephone }}</p>
                            </div>
                            <div class="mb-3 pb-3 border-bottom">
                                <small class="text-muted d-block mb-1">Jenis Kelamin</small>
                                <p class="mb-0 fw-semibold">{{ $user->gender }}</p>
                            </div>
                            <div class="mb-0">
                                <small class="text-muted d-block mb-1">Alamat</small>
                                <p class="mb-0 fw-semibold">{{ $user->address }}</p>
                            </div>
                        </div>

                        {{-- Photo Upload --}}
                        <div class="mt-4 pt-4 border-top">
                            <h6 class="fw-bold mb-3">Foto Profil</h6>
                            
                            <form action="{{ route('profile.photo.update') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                
                                <div class="mb-3">
                                    <input type="file" 
                                           name="photo" 
                                           class="form-control @error('photo') is-invalid @enderror" 
                                           id="photoInput"
                                           accept="image/jpeg,image/png,image/jpg"
                                           onchange="previewPhoto(event)">
                                    @error('photo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Format: JPG, PNG. Max: 2MB</small>
                                </div>

                                <button type="submit" class="btn btn-primary btn-sm w-100">
                                    <i class="bi bi-upload me-1"></i>Upload Foto
                                </button>
                            </form>

                            @if(isset($user->photo))
                                <form action="{{ route('profile.photo.delete') }}" method="POST" class="mt-2"
                                      onsubmit="return confirm('Yakin ingin menghapus foto profil?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm w-100">
                                        <i class="bi bi-trash me-1"></i>Hapus Foto
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Forms Column --}}
            <div class="col-lg-8">
                {{-- Edit Profile Card --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4">
                            <i class="bi bi-person-badge me-2 text-primary"></i>Edit Profil
                        </h5>

                        <form action="{{ route('profile.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="name" class="form-label fw-semibold">Nama Lengkap</label>
                                    <input type="text" 
                                           name="name" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           value="{{ old('name', $user->name) }}">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="number_type" class="form-label fw-semibold">Tipe Nomor</label>
                                    <select name="number_type" 
                                            id="number_type" 
                                            class="form-select @error('number_type') is-invalid @enderror">
                                        @foreach (\App\Models\User::NUMBER_TYPES as $numberType)
                                            <option @selected(old('number_type', $user->number_type) === $numberType) 
                                                    value="{{ $numberType }}">
                                                {{ $numberType }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('number_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="number" class="form-label fw-semibold">Nomor</label>
                                    <input type="number" 
                                           name="number" 
                                           class="form-control @error('number') is-invalid @enderror" 
                                           id="number"
                                           value="{{ old('number', $user->number) }}">
                                    @error('number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label for="address" class="form-label fw-semibold">Alamat</label>
                                    <textarea name="address" 
                                              class="form-control @error('address') is-invalid @enderror" 
                                              id="address" 
                                              rows="2">{{ old('address', $user->address) }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="telephone" class="form-label fw-semibold">Telepon</label>
                                    <div class="input-group">
                                        <span class="input-group-text">+</span>
                                        <input type="number" 
                                               name="telephone" 
                                               id="telephone" 
                                               class="form-control @error('telephone') is-invalid @enderror"
                                               value="{{ old('telephone', $user->telephone) }}">
                                        @error('telephone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <small class="form-text text-muted">Contoh: 6281234567890</small>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold d-block">Jenis Kelamin</label>
                                    <div class="mt-2">
                                        @foreach (\App\Models\User::GENDERS as $gender)
                                            <div class="form-check form-check-inline">
                                                <input @checked(old('gender', $user->gender) === $gender) 
                                                       class="form-check-input @error('gender') is-invalid @enderror" 
                                                       type="radio" 
                                                       name="gender"
                                                       id="{{ $gender }}" 
                                                       value="{{ $gender }}">
                                                <label class="form-check-label" for="{{ $gender }}">
                                                    {{ $gender }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                    @error('gender')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="bi bi-save me-2"></i>Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Change Password Card --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4">
                            <i class="bi bi-key me-2 text-warning"></i>Ubah Password
                        </h5>

                        <form action="{{ route('profile.password.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="current_password" class="form-label fw-semibold">
                                        Password Saat Ini
                                    </label>
                                    <input type="password" 
                                           name="current_password" 
                                           class="form-control @error('current_password') is-invalid @enderror" 
                                           id="current_password">
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="password" class="form-label fw-semibold">
                                        Password Baru
                                    </label>
                                    <input type="password" 
                                           name="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           id="password">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Minimal 8 karakter</small>
                                </div>

                                <div class="col-md-6">
                                    <label for="password_confirmation" class="form-label fw-semibold">
                                        Konfirmasi Password Baru
                                    </label>
                                    <input type="password" 
                                           name="password_confirmation" 
                                           class="form-control" 
                                           id="password_confirmation">
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-warning px-4">
                                    <i class="bi bi-lock me-2"></i>Ubah Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        function previewPhoto(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('profilePhotoPreview');
                    if (preview) {
                        preview.src = e.target.result;
                    }
                };
                reader.readAsDataURL(file);
            }
        }

        // Auto dismiss alerts
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                if (bsAlert) bsAlert.close();
            });
        }, 5000);
    </script>
</x-app-layout>
BLADE;
    }

    /**
     * Get photo migration stub
     */
    protected function getPhotoMigrationStub()
    {
        return <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('photo')->nullable()->after('gender');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('photo');
        });
    }
};
PHP;
    }

    /**
     * Get routes stub
     */
    protected function getRoutesStub()
    {
        return <<<'PHP'
<?php

use App\Http\Controllers\Admin\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Profile Routes
|--------------------------------------------------------------------------
|
| Auto-generated profile routes for Admin, Pustakawan, and Member
|
*/

// Admin & Pustakawan Profile Routes
Route::middleware(['auth', 'superuser'])->prefix('admin')->name('admin.')->group(function () {
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'index')->name('profile.index');
        Route::put('/profile', 'update')->name('profile.update');
        Route::put('/profile/password', 'updatePassword')->name('profile.password.update');
        Route::put('/profile/photo', 'updatePhoto')->name('profile.photo.update');
        Route::delete('/profile/photo', 'deletePhoto')->name('profile.photo.delete');
    });
});

// Member Profile Routes
Route::middleware(['auth'])->group(function () {
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'index')->name('profile.index');
        Route::put('/profile', 'update')->name('profile.update');
        Route::put('/profile/password', 'updatePassword')->name('profile.password.update');
        Route::put('/profile/photo', 'updatePhoto')->name('profile.photo.update');
        Route::delete('/profile/photo', 'deletePhoto')->name('profile.photo.delete');
    });
});
PHP;
    }
}
