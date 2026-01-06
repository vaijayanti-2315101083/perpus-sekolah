<x-app-layout title="Profil Saya">
    <!-- Spacer for fixed navbar -->
    <div style="height: 56px;"></div>

    <section class="py-5 bg-light min-vh-100">
        <div class="container">
            <!-- Page Header -->
            <div class="mb-4">
                <h1 class="fs-2 fw-bold text-dark">Profil Saya</h1>
                <p class="text-muted">Kelola informasi profil dan keamanan akun Anda</p>
            </div>

            <!-- Success Message -->
            @if ($message = session()->get('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ $message }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row g-4">
                <!-- Left Column: Profile Photo -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <h5 class="fw-bold mb-4">Foto Profil</h5>
                            
                            <!-- Profile Photo -->
                            <div class="mb-4">
                                @if($user->photo)
                                    <img src="{{ asset('storage/' . $user->photo) }}" 
                                         alt="{{ $user->name }}" 
                                         class="rounded-circle mb-3"
                                         style="width: 150px; height: 150px; object-fit: cover;">
                                @else
                                    <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3"
                                         style="width: 150px; height: 150px;">
                                        <i class="bi bi-person-circle text-primary" style="font-size: 5rem;"></i>
                                    </div>
                                @endif
                            </div>

                            <!-- Upload Photo Form -->
                            <form action="{{ route('profile.photo.update') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                
                                <div class="mb-3">
                                    <input type="file" 
                                           name="photo" 
                                           class="form-control form-control-sm" 
                                           accept="image/jpeg,image/png,image/jpg"
                                           id="photoInput">
                                    @error('photo')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                    <small class="text-muted d-block mt-2">
                                        JPG, PNG (Max: 2MB)
                                    </small>
                                </div>

                                <button type="submit" class="btn btn-primary btn-sm w-100 mb-2">
                                    <i class="bi bi-upload me-1"></i>Upload Foto
                                </button>
                            </form>

                            <!-- Delete Photo Form -->
                            @if($user->photo)
                                <form action="{{ route('profile.photo.delete') }}" method="POST" onsubmit="return confirm('Yakin ingin hapus foto profil?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                        <i class="bi bi-trash me-1"></i>Hapus Foto
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    <!-- User Info Card -->
                    <div class="card border-0 shadow-sm mt-4">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-3">Informasi Akun</h5>
                            <div class="mb-3">
                                <small class="text-muted d-block">Role</small>
                                <span class="badge bg-primary">{{ $user->role }}</span>
                            </div>
                            <div class="mb-3">
                                <small class="text-muted d-block">{{ $user->number_type }}</small>
                                <strong>{{ $user->number }}</strong>
                            </div>
                            <div class="mb-3">
                                <small class="text-muted d-block">Jenis Kelamin</small>
                                <strong>{{ $user->gender }}</strong>
                            </div>
                            <div>
                                <small class="text-muted d-block">Bergabung Sejak</small>
                                <strong>{{ $user->created_at->locale('id_ID')->isoFormat('LL') }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Profile Forms -->
                <div class="col-lg-8">
                    <!-- Update Profile Information -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-4">
                                <i class="bi bi-person me-2 text-primary"></i>Informasi Profil
                            </h5>

                            <form action="{{ route('profile.update') }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label for="name" class="form-label fw-semibold">Nama Lengkap</label>
                                        <input type="text" 
                                               name="name" 
                                               class="form-control" 
                                               id="name" 
                                               value="{{ old('name', $user->name) }}"
                                               required>
                                        @error('name')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="col-md-12">
                                        <label for="address" class="form-label fw-semibold">Alamat</label>
                                        <textarea name="address" 
                                                  class="form-control" 
                                                  id="address" 
                                                  rows="3"
                                                  required>{{ old('address', $user->address) }}</textarea>
                                        @error('address')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="col-md-12">
                                        <label for="telephone" class="form-label fw-semibold">No. Telepon</label>
                                        <div class="input-group">
                                            <span class="input-group-text">+</span>
                                            <input type="number" 
                                                   name="telephone" 
                                                   class="form-control" 
                                                   id="telephone" 
                                                   value="{{ old('telephone', $user->telephone) }}"
                                                   required>
                                        </div>
                                        @error('telephone')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save me-1"></i>Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Update Password -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-4">
                                <i class="bi bi-key me-2 text-primary"></i>Ubah Password
                            </h5>

                            <form action="{{ route('profile.password.update') }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label for="current_password" class="form-label fw-semibold">Password Saat Ini</label>
                                        <input type="password" 
                                               name="current_password" 
                                               class="form-control" 
                                               id="current_password"
                                               required>
                                        @error('current_password')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="password" class="form-label fw-semibold">Password Baru</label>
                                        <input type="password" 
                                               name="password" 
                                               class="form-control" 
                                               id="password"
                                               required>
                                        @error('password')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                        <small class="text-muted">Minimal 8 karakter</small>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="password_confirmation" class="form-label fw-semibold">Konfirmasi Password Baru</label>
                                        <input type="password" 
                                               name="password_confirmation" 
                                               class="form-control" 
                                               id="password_confirmation"
                                               required>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-shield-check me-1"></i>Ubah Password
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
        .card {
            border-radius: 1rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1) !important;
        }

        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        .btn {
            border-radius: 0.5rem;
            padding: 0.5rem 1.5rem;
            font-weight: 500;
        }

        .badge {
            font-weight: 500;
            padding: 0.5rem 0.75rem;
        }
    </style>
</x-app-layout>