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