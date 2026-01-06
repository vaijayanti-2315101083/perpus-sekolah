<x-app-layout>
    <!-- Spacer for fixed navbar -->
    <div style="height: 56px;"></div>
    @auth
        @if (in_array(auth()->user()->role, [\App\Models\User::ROLES['Admin'], \App\Models\User::ROLES['Librarian']]))
            <div style="height: 48px;"></div>
        @endif
    @endauth

    <!-- Alert Messages -->
    @if ($message = session()->get('success'))
        <div class="container mt-4">
            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    @error('default')
        <div class="container mt-4">
            <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @enderror

    <!-- Current Borrows Section -->
    <section class="py-5 bg-white">
        <div class="container">
            <div class="d-flex align-items-center mb-4">
                <div class="bg-primary rounded-3 p-2 me-3">
                    <i class="bi bi-book text-white fs-4"></i>
                </div>
                <h2 class="fs-3 fw-bold text-dark mb-0">Sedang Dipinjam</h2>
            </div>

            @if(isset($currentBorrows) && $currentBorrows->count() > 0)
                <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 row-cols-xl-4 g-4">
                    @foreach ($currentBorrows as $currentBorrow)
                        <div class="col">
                            <div class="card h-100 border-0 shadow-sm hover-card">
                                <!-- Status Badge -->
                                <div class="position-absolute top-0 start-0 m-3 z-1">
                                    @if (!$currentBorrow->confirmation)
                                        <span class="badge bg-warning text-dark">
                                            <i class="bi bi-clock-history me-1"></i>Belum Dikonfirmasi
                                        </span>
                                    @else
                                        @switch($currentBorrow->restore?->status)
                                            @case(\App\Models\Restore::STATUSES['Not confirmed'])
                                            @case(\App\Models\Restore::STATUSES['Past due'])
                                                <span class="badge bg-secondary">
                                                    <i class="bi bi-hourglass-split me-1"></i>Menunggu Konfirmasi
                                                </span>
                                            @break

                                            @case(\App\Models\Restore::STATUSES['Fine not paid'])
                                                <span class="badge bg-danger">
                                                    <i class="bi bi-exclamation-circle me-1"></i>Denda
                                                </span>
                                            @break

                                            @default
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle me-1"></i>Terkonfirmasi
                                                </span>
                                        @endswitch
                                    @endif
                                </div>

                                <a href="{{ route('preview', $currentBorrow->book) }}" class="text-decoration-none">
                                    <div class="book-cover-container position-relative overflow-hidden">
                                        <img src="{{ isset($currentBorrow->book->cover) ? asset('storage/' . $currentBorrow->book->cover) : asset('storage/placeholder.png') }}"
                                            alt="{{ $currentBorrow->book->title }}" 
                                            class="card-img-top book-cover">
                                        <div class="book-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center">
                                            <i class="bi bi-eye text-white fs-1"></i>
                                        </div>
                                    </div>
                                </a>

                                <div class="card-body d-flex flex-column">
                                    <a href="{{ route('preview', $currentBorrow->book) }}" class="text-decoration-none">
                                        <h3 class="card-title fs-6 fw-bold text-dark mb-3 line-clamp-2">
                                            {{ $currentBorrow->book->title }}
                                        </h3>
                                    </a>

                                    @if($currentBorrow->restore?->status == \App\Models\Restore::STATUSES['Fine not paid'])
                                        <div class="alert alert-danger py-2 px-3 mb-3" role="alert">
                                            <small class="d-block fw-semibold">Denda Terlambat</small>
                                            <strong class="fs-6">Rp {{ number_format($currentBorrow->restore->fine, 0, ',', '.') }}</strong>
                                        </div>
                                    @endif

                                    <!-- Due Date -->
                                    <div class="mt-auto">
                                        @php
                                            $due = $currentBorrow->borrowed_at->addDays($currentBorrow->duration);
                                            $isOverdue = $due < now();
                                        @endphp
                                        <div class="d-flex align-items-center justify-content-between p-3 rounded-3 {{ $isOverdue ? 'bg-danger bg-opacity-10' : 'bg-primary bg-opacity-10' }}">
                                            <div>
                                                <small class="text-muted d-block">Tenggat</small>
                                                <strong class="text-{{ $isOverdue ? 'danger' : 'primary' }}">
                                                    {{ $due->locale('id_ID')->diffForHumans() }}
                                                </strong>
                                            </div>
                                            <i class="bi bi-{{ $isOverdue ? 'exclamation-circle' : 'calendar-check' }} fs-4 text-{{ $isOverdue ? 'danger' : 'primary' }}"></i>
                                        </div>

                                        <!-- Return Button -->
                                        @if($currentBorrow->confirmation && !isset($currentBorrow->restore))
                                            <form action="{{ route('my-books.update', $currentBorrow) }}" method="POST" class="mt-3" onsubmit="return confirm('Anda yakin ingin mengembalikan buku ini?')">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-primary w-100 btn-sm">
                                                    <i class="bi bi-arrow-return-left me-1"></i>Kembalikan Buku
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 d-flex justify-content-center">
                    {{ $currentBorrows->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-inbox text-muted" style="font-size: 4rem;"></i>
                    <p class="text-muted mt-3">Belum ada buku yang sedang dipinjam</p>
                </div>
            @endif
        </div>
    </section>

    <!-- Recent Borrows Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="d-flex align-items-center mb-4">
                <div class="bg-primary rounded-3 p-2 me-3">
                    <i class="bi bi-clock-history text-white fs-4"></i>
                </div>
                <h2 class="fs-3 fw-bold text-dark mb-0">Riwayat Peminjaman</h2>
            </div>

            @if(isset($recentBorrows) && $recentBorrows->count() > 0)
                <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 row-cols-xl-4 g-4">
                    @foreach ($recentBorrows as $recentBorrow)
                        <div class="col">
                            <a href="{{ route('preview', $recentBorrow->book) }}" class="text-decoration-none">
                                <div class="card h-100 border-0 shadow-sm hover-card">
                                    <div class="book-cover-container position-relative overflow-hidden">
                                        <img src="{{ isset($recentBorrow->book->cover) ? asset('storage/' . $recentBorrow->book->cover) : asset('storage/placeholder.png') }}"
                                            alt="{{ $recentBorrow->book->title }}" 
                                            class="card-img-top book-cover">
                                        <div class="book-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center">
                                            <i class="bi bi-eye text-white fs-1"></i>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <h3 class="card-title fs-6 fw-bold text-dark mb-3 line-clamp-2">
                                            {{ $recentBorrow->book->title }}
                                        </h3>
                                        <div class="d-flex align-items-center text-muted">
                                            <i class="bi bi-calendar3 me-2"></i>
                                            <small>
                                                Dikembalikan pada<br>
                                                <strong class="text-dark">{{ $recentBorrow->restore->returned_at->locale('id_ID')->isoFormat('LL') }}</strong>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-clock-history text-muted" style="font-size: 4rem;"></i>
                    <p class="text-muted mt-3">Belum ada riwayat peminjaman</p>
                </div>
            @endif
        </div>
    </section>

    <style>
        .hover-card {
            transition: all 0.3s ease;
        }

        .hover-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15) !important;
        }

        .book-cover-container {
            position: relative;
            aspect-ratio: 2/3;
            background: #f8f9fa;
        }

        .book-cover {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .hover-card:hover .book-cover {
            transform: scale(1.05);
        }

        .book-overlay {
            background: rgba(0, 0, 0, 0.5);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .hover-card:hover .book-overlay {
            opacity: 1;
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 3em;
        }

        .badge {
            font-weight: 500;
            padding: 0.5rem 0.75rem;
            font-size: 0.75rem;
        }

        .alert {
            border-radius: 0.5rem;
        }

        .card {
            border-radius: 1rem;
            overflow: hidden;
        }

        .bg-primary {
            background-color: #0d6efd !important;
        }

        .text-primary {
            color: #0d6efd !important;
        }

        .btn-primary {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        .btn-primary:hover {
            background-color: #0b5ed7;
            border-color: #0a58ca;
        }

        @media (max-width: 576px) {
            .fs-3 {
                font-size: 1.5rem !important;
            }
        }
    </style>
</x-app-layout>