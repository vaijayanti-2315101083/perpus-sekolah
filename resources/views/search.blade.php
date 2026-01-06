<x-app-layout>
    <!-- Spacer for fixed navbar -->
    <div style="height: 56px;"></div>
    @auth
        @if (in_array(auth()->user()->role, [\App\Models\User::ROLES['Admin'], \App\Models\User::ROLES['Librarian']]))
            <div style="height: 48px;"></div>
        @endif
    @endauth

    {{-- Hero Search Section --}}
    <section class="position-relative overflow-hidden" style="min-height: 50vh; background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);">
        <div class="container py-5">
            <div class="row justify-content-center align-items-center min-vh-50">
                <div class="col-lg-8 text-center text-white">
                    <h1 class="display-4 fw-bold mb-3">Temukan Buku Favorit Anda</h1>
                    @if(request()->query('search'))
                        <p class="fs-5 mb-4 opacity-90">Menampilkan hasil untuk: <span class="fw-bold">"{{ request()->query('search') }}"</span></p>
                    @else
                        <p class="fs-5 mb-4 opacity-90">Jelajahi ribuan koleksi buku digital di perpustakaan kami</p>
                    @endif
                    
                    <form action="{{ route('search') }}" method="GET">
                        <div class="input-group input-group-lg shadow-lg mx-auto" style="max-width: 650px;">
                            <input type="text" 
                                   name="search" 
                                   class="form-control border-0 ps-4" 
                                   placeholder="Cari judul buku, penulis, atau kategori..."
                                   value="{{ request()->query('search') }}"
                                   style="height: 65px; border-radius: 50px 0 0 50px;">
                            <button type="submit" 
                                    class="btn btn-light px-5 border-0 fw-semibold" 
                                    style="border-radius: 0 50px 50px 0;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="me-2" viewBox="0 0 16 16">
                                    <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                                </svg>
                                Cari
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    {{-- Results Section --}}
    <section class="py-5 bg-light">
        <div class="container">
            @if($books->isNotEmpty())
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary rounded" style="width: 5px; height: 45px;"></div>
                        <div class="ms-3">
                            <h2 class="fs-3 fw-bold mb-0 text-dark">Hasil Pencarian</h2>
                            <p class="text-muted mb-0 small">Ditemukan {{ $books->total() }} buku</p>
                        </div>
                    </div>
                    <span class="badge bg-white text-dark border px-3 py-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="me-1" viewBox="0 0 16 16">
                            <path d="M1 2.5A1.5 1.5 0 0 1 2.5 1h3A1.5 1.5 0 0 1 7 2.5v3A1.5 1.5 0 0 1 5.5 7h-3A1.5 1.5 0 0 1 1 5.5zm8 0A1.5 1.5 0 0 1 10.5 1h3A1.5 1.5 0 0 1 15 2.5v3A1.5 1.5 0 0 1 13.5 7h-3A1.5 1.5 0 0 1 9 5.5zm-8 8A1.5 1.5 0 0 1 2.5 9h3A1.5 1.5 0 0 1 7 10.5v3A1.5 1.5 0 0 1 5.5 15h-3A1.5 1.5 0 0 1 1 13.5zm8 0A1.5 1.5 0 0 1 10.5 9h3a1.5 1.5 0 0 1 1.5 1.5v3a1.5 1.5 0 0 1-1.5 1.5h-3A1.5 1.5 0 0 1 9 13.5z"/>
                        </svg>
                        Halaman {{ $books->currentPage() }} dari {{ $books->lastPage() }}
                    </span>
                </div>
            @endif

            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-4">
                @forelse ($books as $book)
                    <div class="col">
                        <a href="{{ route('preview', $book) }}" class="text-decoration-none d-block">
                            <div class="card border-0 shadow-sm h-100 hover-card">
                                <div class="position-relative overflow-hidden rounded-top" style="aspect-ratio: 2/3;">
                                    <img src="{{ isset($book->cover) ? asset('storage/' . $book->cover) : asset('storage/placeholder.png') }}"
                                        alt="{{ $book->title }}" 
                                        class="w-100 h-100 object-fit-cover">
                                    <div class="position-absolute top-0 start-0 end-0 p-2 bg-gradient" style="background: linear-gradient(to bottom, rgba(0,0,0,0.6) 0%, transparent 100%);">
                                        <span class="badge bg-primary rounded-pill px-2 py-1 small">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" class="me-1" viewBox="0 0 16 16">
                                                <path d="M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.746c-.935-.53-2.12-.603-3.213-.493-1.18.12-2.37.461-3.287.811zm7.5-.141c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783"/>
                                            </svg>
                                            Buku
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body p-3">
                                    <h5 class="card-title fw-bold mb-2 text-dark" style="font-size: 0.95rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; min-height: 2.8em;">
                                        {{ $book->title }}
                                    </h5>
                                    <div class="d-flex align-items-center text-muted small mb-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="me-1 flex-shrink-0" viewBox="0 0 16 16">
                                            <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z"/>
                                        </svg>
                                        <span class="text-truncate">{{ $book->writer }}</span>
                                    </div>
                                    <div class="pt-2 border-top">
                                        <span class="badge bg-light text-primary border border-primary small">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" class="me-1" viewBox="0 0 16 16">
                                                <path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2"/>
                                            </svg>
                                            Lihat Detail
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="text-center py-5 bg-white rounded-4 shadow-sm">
                            <div class="mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" fill="currentColor" class="text-muted opacity-50" viewBox="0 0 16 16">
                                    <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                                </svg>
                            </div>
                            <h2 class="fs-2 fw-bold text-dark mb-3">Tidak Ada Buku Ditemukan</h2>
                            <p class="text-muted mb-4 fs-6">Maaf, kami tidak dapat menemukan buku yang sesuai dengan pencarian Anda.<br>Coba gunakan kata kunci yang berbeda.</p>
                            <a href="{{ route('search') }}" class="btn btn-primary rounded-pill px-4 py-2 shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-2" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8"/>
                                </svg>
                                Kembali ke Pencarian
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>

            @if ($books->isNotEmpty())
                <div class="mt-5 d-flex justify-content-center">
                    <nav aria-label="Page navigation">
                        {{ $books->withQueryString()->links() }}
                    </nav>
                </div>
            @endif
        </div>
    </section>

    <style>
        .hover-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .hover-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(13, 110, 253, 0.2) !important;
        }

        .object-fit-cover {
            object-fit: cover;
        }

        /* Modern Pagination Styling */
        .pagination {
            gap: 8px;
            margin: 0;
        }
        
        .pagination .page-link {
            border-radius: 12px;
            border: 2px solid #e0e0e0;
            color: #0d6efd;
            padding: 10px 18px;
            font-weight: 600;
            transition: all 0.3s ease;
            background-color: white;
        }
        
        .pagination .page-link:hover {
            background-color: #0d6efd;
            color: white;
            border-color: #0d6efd;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
        }
        
        .pagination .page-item.active .page-link {
            background-color: #0d6efd;
            border-color: #0d6efd;
            color: white;
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
        }
        
        .pagination .page-item.disabled .page-link {
            color: #6c757d;
            background-color: #f8f9fa;
            border-color: #e0e0e0;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .display-4 {
                font-size: 2rem;
            }
            
            .input-group-lg {
                max-width: 100% !important;
            }
            
            .pagination .page-link {
                padding: 8px 12px;
                font-size: 0.875rem;
            }
        }

        @media (max-width: 576px) {
            .card-title {
                font-size: 0.85rem !important;
            }
        }
    </style>
</x-app-layout>