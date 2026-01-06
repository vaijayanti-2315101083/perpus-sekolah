<x-app-layout>
    <!-- Spacer for fixed navbar -->
    <div style="height: 56px;"></div>
    @auth
        @if (in_array(auth()->user()->role, [\App\Models\User::ROLES['Admin'], \App\Models\User::ROLES['Librarian']]))
            <div style="height: 48px;"></div>
        @endif
    @endauth

    {{-- Hero Section --}}
    <section class="position-relative overflow-hidden" style="min-height: 90vh; background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);">
        <div class="container py-5">
            <div class="row align-items-center min-vh-75 mt-5">
                <div class="col-lg-6 text-white mb-5 mb-lg-0">
                    <h1 class="display-3 fw-bold mb-4" style="line-height: 1.2;">
                        Perpustakaan Digital Modern
                    </h1>
                    <p class="fs-5 mb-4 opacity-90" style="max-width: 500px;">
                        Akses ribuan koleksi buku digital dengan mudah. Pinjam, baca, dan kembalikan buku favorit Anda kapan saja, di mana saja.
                    </p>
                    
                    <form action="{{ route('search') }}" method="GET" class="mb-5">
                        <div class="input-group input-group-lg shadow-lg" style="max-width: 550px;">
                            <input type="text" 
                                   name="search" 
                                   class="form-control border-0 ps-4" 
                                   placeholder="Cari judul buku, penulis, kategori..."
                                   style="height: 65px; border-radius: 50px 0 0 50px;">
                            <button type="submit" 
                                    class="btn btn-light px-4 border-0" 
                                    style="border-radius: 0 50px 50px 0;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                                </svg>
                            </button>
                        </div>
                    </form>

                    {{-- Stats --}}
                    <div class="row g-4 mt-3">
                        <div class="col-4">
                            <div class="text-white">
                                <h3 class="display-6 fw-bold mb-0">10K+</h3>
                                <p class="small mb-0 opacity-75">Buku Digital</p>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="text-white">
                                <h3 class="display-6 fw-bold mb-0">5K+</h3>
                                <p class="small mb-0 opacity-75">Pengguna Aktif</p>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="text-white">
                                <h3 class="display-6 fw-bold mb-0">24/7</h3>
                                <p class="small mb-0 opacity-75">Akses Online</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="position-relative">
                        <div class="bg-white rounded-4 shadow-lg p-4" style="backdrop-filter: blur(10px);">
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="bg-light rounded-3 p-3 text-center" style="height: 180px;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="#0d6efd" viewBox="0 0 16 16">
                                            <path d="M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.746c-.935-.53-2.12-.603-3.213-.493-1.18.12-2.37.461-3.287.811zm7.5-.141c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783"/>
                                        </svg>
                                        <h6 class="fw-bold mt-3 mb-1">Koleksi Lengkap</h6>
                                        <p class="small text-muted mb-0">Ribuan buku tersedia</p>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="bg-light rounded-3 p-3 text-center" style="height: 180px;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="#0d6efd" viewBox="0 0 16 16">
                                            <path d="M8 16c3.314 0 6-2 6-5.5 0-1.5-.5-4-2.5-6 .25 1.5-1.25 2-1.25 2C11 4 9 .5 6 0c.357 2 .5 4-2 6-1.25 1-2 2.729-2 4.5C2 14 4.686 16 8 16m0-1c-1.657 0-3-1-3-2.75 0-.75.25-2 1.25-3C6.125 10 7 10.5 7 10.5c-.375-1.25.5-3.25 2-3.5-.179 1-.25 2 1 3 .625.5 1 1.364 1 2.25C11 14 9.657 15 8 15"/>
                                        </svg>
                                        <h6 class="fw-bold mt-3 mb-1">Paling Populer</h6>
                                        <p class="small text-muted mb-0">Buku terfavorit</p>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="bg-light rounded-3 p-3 text-center" style="height: 180px;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="#0d6efd" viewBox="0 0 16 16">
                                            <path d="M11 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1zM5 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z"/>
                                            <path d="M8 14a1 1 0 1 0 0-2 1 1 0 0 0 0 2"/>
                                        </svg>
                                        <h6 class="fw-bold mt-3 mb-1">Multi Platform</h6>
                                        <p class="small text-muted mb-0">Akses dimana saja</p>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="bg-light rounded-3 p-3 text-center" style="height: 180px;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="#0d6efd" viewBox="0 0 16 16">
                                            <path d="M5.338 1.59a61 61 0 0 0-2.837.856.48.48 0 0 0-.328.39c-.554 4.157.726 7.19 2.253 9.188a10.7 10.7 0 0 0 2.287 2.233c.346.244.652.42.893.533q.18.085.293.118a1 1 0 0 0 .101.025 1 1 0 0 0 .1-.025q.114-.034.294-.118c.24-.113.547-.29.893-.533a10.7 10.7 0 0 0 2.287-2.233c1.527-1.997 2.807-5.031 2.253-9.188a.48.48 0 0 0-.328-.39c-.651-.213-1.75-.56-2.837-.855C9.552 1.29 8.531 1.067 8 1.067c-.53 0-1.552.223-2.662.524zM5.072.56C6.157.265 7.31 0 8 0s1.843.265 2.928.56c1.11.3 2.229.655 2.887.87a1.54 1.54 0 0 1 1.044 1.262c.596 4.477-.787 7.795-2.465 9.99a11.8 11.8 0 0 1-2.517 2.453 7 7 0 0 1-1.048.625c-.28.132-.581.24-.829.24s-.548-.108-.829-.24a7 7 0 0 1-1.048-.625 11.8 11.8 0 0 1-2.517-2.453C1.928 10.487.545 7.169 1.141 2.692A1.54 1.54 0 0 1 2.185 1.43 63 63 0 0 1 5.072.56"/>
                                            <path d="M10.854 5.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7.5 7.793l2.646-2.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <h6 class="fw-bold mt-3 mb-1">Aman & Gratis</h6>
                                        <p class="small text-muted mb-0">Tanpa biaya</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Popular Books Section --}}
    <section class="py-5 bg-white">
        <div class="container py-4">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="d-flex align-items-center">
                    <div class="bg-primary rounded" style="width: 5px; height: 40px;"></div>
                    <h2 class="fs-2 fw-bold mb-0 ms-3 text-dark">Paling Populer</h2>
                </div>
                <a href="{{ route('search') }}" class="btn btn-outline-primary rounded-pill px-4">
                    Lihat Semua
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="ms-2" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8"/>
                    </svg>
                </a>
            </div>

            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-5 g-4">
                @foreach ($popularBooks as $popularBook)
                    <div class="col">
                        <a href="{{ route('preview', $popularBook) }}" class="text-decoration-none d-block">
                            <div class="card border-0 shadow-sm h-100 hover-card">
                                <div class="position-relative overflow-hidden rounded-top" style="aspect-ratio: 2/3;">
                                    <img src="{{ isset($popularBook->cover) ? asset('storage/' . $popularBook->cover) : asset('storage/placeholder.png') }}"
                                        alt="{{ $popularBook->title }}" 
                                        class="w-100 h-100 object-fit-cover">
                                    <div class="position-absolute top-0 end-0 m-2">
                                        <span class="badge bg-danger rounded-pill px-3 py-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="me-1" viewBox="0 0 16 16">
                                                <path d="M8 16c3.314 0 6-2 6-5.5 0-1.5-.5-4-2.5-6 .25 1.5-1.25 2-1.25 2C11 4 9 .5 6 0c.357 2 .5 4-2 6-1.25 1-2 2.729-2 4.5C2 14 4.686 16 8 16m0-1c-1.657 0-3-1-3-2.75 0-.75.25-2 1.25-3C6.125 10 7 10.5 7 10.5c-.375-1.25.5-3.25 2-3.5-.179 1-.25 2 1 3 .625.5 1 1.364 1 2.25C11 14 9.657 15 8 15"/>
                                            </svg>
                                            Populer
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body p-3">
                                    <h5 class="card-title fw-bold mb-2 text-dark text-truncate" style="font-size: 0.95rem;">
                                        {{ $popularBook->title }}
                                    </h5>
                                    <div class="d-flex align-items-center text-muted small">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="me-1" viewBox="0 0 16 16">
                                            <path fill-rule="evenodd" d="M10.854 5.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7.5 7.793l2.646-2.647a.5.5 0 0 1 .708 0"/>
                                            <path d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v13.5a.5.5 0 0 1-.777.416L8 13.101l-5.223 2.815A.5.5 0 0 1 2 15.5zm2-1a1 1 0 0 0-1 1v12.566l4.723-2.482a.5.5 0 0 1 .554 0L13 14.566V2a1 1 0 0 0-1-1z"/>
                                        </svg>
                                        <span class="fw-semibold text-primary">{{ $popularBook->borrows_count }}</span>
                                        <span class="ms-1">peminjam</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Newest Books Section --}}
    <section class="py-5 bg-light">
        <div class="container py-4">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="d-flex align-items-center">
                    <div class="bg-success rounded" style="width: 5px; height: 40px;"></div>
                    <h2 class="fs-2 fw-bold mb-0 ms-3 text-dark">Buku Terbaru</h2>
                </div>
                <a href="{{ route('search') }}" class="btn btn-outline-success rounded-pill px-4">
                    Lihat Semua
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="ms-2" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8"/>
                    </svg>
                </a>
            </div>

            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-5 g-4">
                @foreach ($newestBooks as $newestBook)
                    <div class="col">
                        <a href="{{ route('preview', $newestBook) }}" class="text-decoration-none d-block">
                            <div class="card border-0 shadow-sm h-100 hover-card">
                                <div class="position-relative overflow-hidden rounded-top" style="aspect-ratio: 2/3;">
                                    <img src="{{ isset($newestBook->cover) ? asset('storage/' . $newestBook->cover) : asset('storage/placeholder.png') }}"
                                        alt="{{ $newestBook->title }}" 
                                        class="w-100 h-100 object-fit-cover">
                                    <div class="position-absolute top-0 end-0 m-2">
                                        <span class="badge bg-success rounded-pill px-3 py-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="me-1" viewBox="0 0 16 16">
                                                <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                                            </svg>
                                            Baru
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body p-3">
                                    <h5 class="card-title fw-bold mb-2 text-dark text-truncate" style="font-size: 0.95rem;">
                                        {{ $newestBook->title }}
                                    </h5>
                                    <div class="d-flex align-items-center text-muted small">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="me-1" viewBox="0 0 16 16">
                                            <path d="M10.854 7.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7.5 9.793l2.646-2.647a.5.5 0 0 1 .708 0"/>
                                            <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z"/>
                                        </svg>
                                        <span>{{ $newestBook->created_at->locale('id_ID')->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Features Section --}}
    <section class="py-5 bg-white">
        <div class="container py-4">
            <div class="text-center mb-5">
                <h2 class="fs-2 fw-bold mb-3">Kenapa Memilih Kami?</h2>
                <p class="text-muted fs-6">Pengalaman membaca digital yang lebih baik dan modern</p>
            </div>

            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="text-center p-4 h-100">
                        <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#0d6efd" viewBox="0 0 16 16">
                                <path d="M11.251.068a.5.5 0 0 1 .227.58L9.677 6.5H13a.5.5 0 0 1 .364.843l-8 8.5a.5.5 0 0 1-.842-.49L6.323 9.5H3a.5.5 0 0 1-.364-.843l8-8.5a.5.5 0 0 1 .615-.09z"/>
                            </svg>
                        </div>
                        <h5 class="fw-bold mb-2">Proses Cepat</h5>
                        <p class="text-muted small mb-0">Pinjam dan kembalikan buku dalam hitungan detik</p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="text-center p-4 h-100">
                        <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#0d6efd" viewBox="0 0 16 16">
                                <path d="M12.136.326A1.5 1.5 0 0 1 14 1.78V3h.5A1.5 1.5 0 0 1 16 4.5v9a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 13.5v-9a1.5 1.5 0 0 1 1.432-1.499zM5.562 3H13V1.78a.5.5 0 0 0-.621-.484zM1.5 4a.5.5 0 0 0-.5.5v9a.5.5 0 0 0 .5.5h13a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 0-.5-.5z"/>
                            </svg>
                        </div>
                        <h5 class="fw-bold mb-2">100% Gratis</h5>
                        <p class="text-muted small mb-0">Tanpa biaya keanggotaan atau peminjaman</p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="text-center p-4 h-100">
                        <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#0d6efd" viewBox="0 0 16 16">
                                <path d="M11 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1zM5 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z"/>
                                <path d="M8 14a1 1 0 1 0 0-2 1 1 0 0 0 0 2"/>
                            </svg>
                        </div>
                        <h5 class="fw-bold mb-2">Multi Device</h5>
                        <p class="text-muted small mb-0">Baca di smartphone, tablet, atau komputer</p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="text-center p-4 h-100">
                        <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#0d6efd" viewBox="0 0 16 16">
                                <path d="M11.534 7h3.932a.25.25 0 0 1 .192.41l-1.966 2.36a.25.25 0 0 1-.384 0l-1.966-2.36a.25.25 0 0 1 .192-.41m-11 2h3.932a.25.25 0 0 0 .192-.41L2.692 6.23a.25.25 0 0 0-.384 0L.342 8.59A.25.25 0 0 0 .534 9"/>
                                <path fill-rule="evenodd" d="M8 3c-1.552 0-2.94.707-3.857 1.818a.5.5 0 1 1-.771-.636A6.002 6.002 0 0 1 13.917 7H12.9A5 5 0 0 0 8 3M3.1 9a5.002 5.002 0 0 0 8.757 2.182.5.5 0 1 1 .771.636A6.002 6.002 0 0 1 2.083 9z"/>
                            </svg>
                        </div>
                        <h5 class="fw-bold mb-2">Selalu Update</h5>
                        <p class="text-muted small mb-0">Koleksi buku terbaru ditambahkan rutin</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- FAQ Section --}}
    <section class="py-5 bg-light">
        <div class="container py-4">
            <div class="text-center mb-5">
                <h2 class="fs-2 fw-bold mb-3">Pertanyaan Umum</h2>
                <p class="text-muted fs-6">Jawaban untuk pertanyaan yang sering ditanyakan</p>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="accordion accordion-flush" id="faqAccordion">
                        <div class="accordion-item bg-white rounded-3 mb-3 border-0 shadow-sm">
                            <h3 class="accordion-header">
                                <button class="accordion-button fw-semibold rounded-3" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    Bagaimana cara meminjam buku?
                                </button>
                            </h3>
                            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted">
                                    Anda perlu mendaftar atau login terlebih dahulu, kemudian cari buku yang Anda inginkan melalui halaman pencarian. Klik pada buku tersebut dan pilih opsi "Pinjam Buku". Buku akan langsung tersedia di halaman "Buku-ku".
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item bg-white rounded-3 mb-3 border-0 shadow-sm">
                            <h3 class="accordion-header">
                                <button class="accordion-button collapsed fw-semibold rounded-3" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    Berapa lama durasi peminjaman buku?
                                </button>
                            </h3>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted">
                                    Durasi peminjaman standar adalah 14 hari. Anda dapat melihat tanggal jatuh tempo di halaman "Buku-ku". Pastikan untuk mengembalikan buku tepat waktu agar dapat meminjam buku lainnya.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item bg-white rounded-3 mb-3 border-0 shadow-sm">
                            <h3 class="accordion-header">
                                <button class="accordion-button collapsed fw-semibold rounded-3" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    Apakah ada batasan jumlah buku yang bisa dipinjam?
                                </button>
                            </h3>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted">
                                    Ya, setiap pengguna dapat meminjam maksimal 3 buku secara bersamaan. Setelah mengembalikan buku, Anda dapat meminjam buku lain sesuai kebutuhan.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item bg-white rounded-3 mb-3 border-0 shadow-sm">
                            <h3 class="accordion-header">
                                <button class="accordion-button collapsed fw-semibold rounded-3" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                    Bagaimana cara mengembalikan buku?
                                </button>
                            </h3>
                            <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted">
                                    Untuk mengembalikan buku, kunjungi halaman "Buku-ku", pilih buku yang ingin dikembalikan, dan klik tombol "Kembalikan". Buku akan langsung dikembalikan ke perpustakaan.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item bg-white rounded-3 mb-3 border-0 shadow-sm">
                            <h3 class="accordion-header">
                                <button class="accordion-button collapsed fw-semibold rounded-3" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                                    Apakah ada denda keterlambatan?
                                </button>
                            </h3>
                            <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted">
                                    Saat ini sistem kami belum menerapkan denda keterlambatan. Namun, kami sangat mengharapkan Anda untuk mengembalikan buku tepat waktu agar pengguna lain dapat menikmati buku tersebut.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item bg-white rounded-3 border-0 shadow-sm">
                            <h3 class="accordion-header">
                                <button class="accordion-button collapsed fw-semibold rounded-3" type="button" data-bs-toggle="collapse" data-bs-target="#faq6">
                                    Apakah perlu biaya untuk mendaftar?
                                </button>
                            </h3>
                            <div id="faq6" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted">
                                    Tidak, pendaftaran dan semua layanan perpustakaan online kami sepenuhnya gratis. Anda hanya perlu membuat akun dengan email yang valid untuk mulai meminjam buku.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="bg-dark text-white py-5">
        <div class="container py-4">
            <div class="row g-4">
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <h5 class="fw-bold mb-3">Perpustakaan Online</h5>
                    <p class="text-white-50 mb-4">Platform peminjaman buku digital yang memudahkan Anda mengakses ribuan koleksi buku kapan saja dan di mana saja.</p>
                    <div class="d-flex gap-2">
                        <a href="#" class="btn btn-outline-light btn-sm rounded-circle" style="width: 40px; height: 40px; padding: 0; display: flex; align-items: center; justify-content: center;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951"/>
                            </svg>
                        </a>
                        <a href="#" class="btn btn-outline-light btn-sm rounded-circle" style="width: 40px; height: 40px; padding: 0; display: flex; align-items: center; justify-content: center;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.9 3.9 0 0 0-1.417.923A3.9 3.9 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.9 3.9 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.9 3.9 0 0 0-.923-1.417A3.9 3.9 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599s.453.546.598.92c.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.5 2.5 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.5 2.5 0 0 1-.92-.598 2.5 2.5 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233s.008-2.388.046-3.231c.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92s.546-.453.92-.598c.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92m-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217m0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334"/>
                            </svg>
                        </a>
                        <a href="#" class="btn btn-outline-light btn-sm rounded-circle" style="width: 40px; height: 40px; padding: 0; display: flex; align-items: center; justify-content: center;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M5.026 15c6.038 0 9.341-5.003 9.341-9.334q.002-.211-.006-.422A6.7 6.7 0 0 0 16 3.542a6.7 6.7 0 0 1-1.889.518 3.3 3.3 0 0 0 1.447-1.817 6.5 6.5 0 0 1-2.087.793A3.286 3.286 0 0 0 7.875 6.03a9.32 9.32 0 0 1-6.767-3.429 3.29 3.29 0 0 0 1.018 4.382A3.3 3.3 0 0 1 .64 6.575v.045a3.29 3.29 0 0 0 2.632 3.218 3.2 3.2 0 0 1-.865.115 3 3 0 0 1-.614-.057 3.28 3.28 0 0 0 3.067 2.277A6.6 6.6 0 0 1 .78 13.58a6 6 0 0 1-.78-.045A9.34 9.34 0 0 0 5.026 15"/>
                            </svg>
                        </a>
                    </div>
                </div>

                <div class="col-lg-2 col-md-3 col-6">
                    <h6 class="fw-bold mb-3">Menu</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="{{ route('home') }}" class="text-white-50 text-decoration-none">Beranda</a></li>
                        <li class="mb-2"><a href="{{ route('search') }}" class="text-white-50 text-decoration-none">Cari Buku</a></li>
                        @auth
                            <li class="mb-2"><a href="{{ route('my-books.index') }}" class="text-white-50 text-decoration-none">Buku Saya</a></li>
                        @endauth
                    </ul>
                </div>

                <div class="col-lg-2 col-md-3 col-6">
                    <h6 class="fw-bold mb-3">Kategori</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Fiksi</a></li>
                        <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Non-Fiksi</a></li>
                        <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Pendidikan</a></li>
                        <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Teknologi</a></li>
                    </ul>
                </div>

                <div class="col-lg-4 col-md-6">
                    <h6 class="fw-bold mb-3">Kontak</h6>
                    <ul class="list-unstyled text-white-50">
                        <li class="mb-2 d-flex align-items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-2 mt-1 flex-shrink-0" viewBox="0 0 16 16">
                                <path d="M12.166 8.94c-.524 1.062-1.234 2.12-1.96 3.07A32 32 0 0 1 8 14.58a32 32 0 0 1-2.206-2.57c-.726-.95-1.436-2.008-1.96-3.07C3.304 7.867 3 6.862 3 6a5 5 0 0 1 10 0c0 .862-.305 1.867-.834 2.94M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10"/>
                                <path d="M8 8a2 2 0 1 1 0-4 2 2 0 0 1 0 4m0 1a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/>
                            </svg>
                            <span>Surabaya, East Java, Indonesia</span>
                        </li>
                        <li class="mb-2 d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-2 flex-shrink-0" viewBox="0 0 16 16">
                                <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1zm13 2.383-4.708 2.825L15 11.105zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741M1 11.105l4.708-2.897L1 5.383z"/>
                            </svg>
                            <span>info@perpustakaan.com</span>
                        </li>
                        <li class="mb-2 d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-2 flex-shrink-0" viewBox="0 0 16 16">
                                <path d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.6 17.6 0 0 0 4.168 6.608 17.6 17.6 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.68.68 0 0 0-.58-.122l-2.19.547a1.75 1.75 0 0 1-1.657-.459L5.482 8.062a1.75 1.75 0 0 1-.46-1.657l.548-2.19a.68.68 0 0 0-.122-.58z"/>
                            </svg>
                            <span>+62 812-3456-7890</span>
                        </li>
                    </ul>
                </div>
            </div>

            <hr class="my-4 border-secondary">

            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start text-white-50 small">
                    <p class="mb-0">&copy; 2025 Perpustakaan Online. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-center text-md-end small">
                    <a href="#" class="text-white-50 text-decoration-none me-3">Privacy Policy</a>
                    <a href="#" class="text-white-50 text-decoration-none">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <style>
        .hover-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .hover-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(13, 110, 253, 0.15) !important;
        }

        .object-fit-cover {
            object-fit: cover;
        }

        @media (max-width: 768px) {
            .display-3 {
                font-size: 2.5rem;
            }
        }
    </style>
</x-app-layout>