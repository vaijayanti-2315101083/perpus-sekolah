<x-admin-layout title="Dashboard">
    {{-- Welcome Section --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Selamat Datang
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ Auth::user()->name }}
                            </div>
                            <div class="text-muted small mt-1">
                                @if (Auth::user()->role === 'Admin')
                                    Anda login sebagai <strong>Administrator</strong>. Kelola sistem perpustakaan dengan
                                    bijak.
                                @elseif(Auth::user()->role === 'Pustakawan')
                                    Anda login sebagai <strong>Pustakawan</strong>. Kelola peminjaman dan pengembalian
                                    buku.
                                @else
                                    Anda login sebagai <strong>Member</strong>. Selamat membaca dan belajar!
                                @endif
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-circle fa-3x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="row">
        {{-- Total Books Card --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Buku
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ \App\Models\Book::count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-book fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Available Books Card --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Buku Tersedia
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ \App\Models\Book::where('status', 'Tersedia')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if (Auth::user()->role === 'Member')
            {{-- My Active Borrows Card --}}
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Sedang Dipinjam
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ \App\Models\Borrow::where('user_id', Auth::id())->where('confirmation', 0)->count() }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-bookmark fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Total My Borrows Card --}}
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Total Peminjaman
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ \App\Models\Borrow::where('user_id', Auth::id())->count() }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-history fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            {{-- Active Borrows Card (Admin/Pustakawan) --}}
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Peminjaman Aktif
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ \App\Models\Borrow::where('confirmation', 0)->count() }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Total Users Card --}}
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Total Pengguna
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ \App\Models\User::count() }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- CTA Section for Member --}}
    @if (Auth::user()->role === 'Member')
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card shadow" style="border-left: 5px solid #4e73df;">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h5 class="font-weight-bold text-primary mb-2">
                                    <i class="fas fa-book-reader mr-2"></i>Mulai Membaca Sekarang!
                                </h5>
                                <p class="text-gray-700 mb-3">
                                    Jelajahi ribuan koleksi buku digital kami. Temukan buku favorit Anda dan mulai
                                    petualangan membaca yang menyenangkan.
                                </p>
                                <a href="{{ route('home') }}" class="btn btn-primary btn-icon-split">
                                    <span class="icon text-white-50">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <span class="text">Cari Buku Sekarang</span>
                                </a>
                            </div>
                            <div class="col-md-4 text-center">
                                <i class="fas fa-book-open fa-5x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Quick Actions for Admin/Pustakawan --}}
    @if (Auth::user()->role === 'Admin' || Auth::user()->role === 'Pustakawan')
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Aksi Cepat</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @if (Auth::user()->role === 'Admin')
                                <div class="col-md-3 mb-3">
                                    <a href="{{ route('admin.librarians.index') }}"
                                        class="btn btn-outline-primary btn-block py-3">
                                        <i class="fas fa-user-tie fa-2x d-block mb-2"></i>
                                        <strong>Kelola Pustakawan</strong>
                                    </a>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <a href="{{ route('admin.members.index') }}"
                                        class="btn btn-outline-success btn-block py-3">
                                        <i class="fas fa-users fa-2x d-block mb-2"></i>
                                        <strong>Kelola Member</strong>
                                    </a>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <a href="{{ route('admin.books.index') }}"
                                        class="btn btn-outline-info btn-block py-3">
                                        <i class="fas fa-book fa-2x d-block mb-2"></i>
                                        <strong>Kelola Buku</strong>
                                    </a>
                                </div>
                            @elseif(Auth::user()->role === 'Pustakawan')
                                <div class="col-md-4 mb-3">
                                    <a href="{{ route('admin.members.index') }}"
                                        class="btn btn-outline-success btn-block py-3">
                                        <i class="fas fa-users fa-2x d-block mb-2"></i>
                                        <strong>Kelola Member</strong>
                                    </a>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <a href="{{ route('admin.books.index') }}"
                                        class="btn btn-outline-info btn-block py-3">
                                        <i class="fas fa-book fa-2x d-block mb-2"></i>
                                        <strong>Kelola Buku</strong>
                                    </a>
                                </div>
                            @endif
                            <div class="col-md-{{ Auth::user()->role === 'Admin' ? '3' : '4' }} mb-3">
                                <a href="{{ route('admin.borrows.index') }}"
                                    class="btn btn-outline-warning btn-block py-3">
                                    <i class="fas fa-clipboard-list fa-2x d-block mb-2"></i>
                                    <strong>Kelola Peminjaman</strong>
                                </a>
                            </div>
                            <div class="col-md-{{ Auth::user()->role === 'Admin' ? '3' : '4' }} mb-3">
                                <a href="{{ route('admin.returns.index') }}"
                                    class="btn btn-outline-danger btn-block py-3">
                                    <i class="fas fa-undo fa-2x d-block mb-2"></i>
                                    <strong>Kelola Pengembalian</strong>
                                </a>
                            </div>
                            <div class="col-md-4 mb-3">
                                <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-block py-3">
                                    <i class="fas fa-home fa-2x d-block mb-2"></i>
                                    <strong>Ke Perpustakaan</strong>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Recent Activity for Member --}}
    @if (Auth::user()->role === 'Member')
        <div class="row">
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Peminjaman Terakhir Saya</h6>
                    </div>
                    <div class="card-body">
                        @php
                            $recentBorrows = \App\Models\Borrow::with('book')
                                ->where('user_id', Auth::id())
                                ->latest()
                                ->take(5)
                                ->get();
                        @endphp

                        @if ($recentBorrows->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Judul Buku</th>
                                            <th>Tanggal Pinjam</th>
                                            <th>Durasi</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($recentBorrows as $borrow)
                                            <tr>
                                                <td>{{ $borrow->book->title }}</td>
                                                <td>{{ \Carbon\Carbon::parse($borrow->borrowed_at)->format('d M Y') }}
                                                </td>
                                                <td>{{ $borrow->duration }} hari</td>
                                                <td>
                                                    @if ($borrow->confirmation)
                                                        <span class="badge badge-success">Dikonfirmasi</span>
                                                    @else
                                                        <span class="badge badge-warning">Menunggu</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
                                <p class="text-muted">Belum ada riwayat peminjaman.</p>
                                <a href="{{ route('home') }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-search mr-1"></i>Cari Buku
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Recent Borrows for Admin/Pustakawan --}}
    @if (Auth::user()->role === 'Admin' || Auth::user()->role === 'Pustakawan')
        <div class="row">
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Peminjaman Terbaru</h6>
                        {{-- <a href="{{ route('borrows.index') }}" class="btn btn-sm btn-primary">Lihat Semua</a> --}}
                    </div>
                    <div class="card-body">
                        @php
                            $recentBorrows = \App\Models\Borrow::with(['book', 'user'])
                                ->orderBy('borrowed_at', 'desc')
                                ->take(5)
                                ->get();
                        @endphp

                        @if ($recentBorrows->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Peminjam</th>
                                            <th>Judul Buku</th>
                                            <th>Tanggal Pinjam</th>
                                            <th>Durasi</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($recentBorrows as $borrow)
                                            <tr>
                                                <td>{{ $borrow->user->name }}</td>
                                                <td>{{ $borrow->book->title }}</td>
                                                <td>{{ \Carbon\Carbon::parse($borrow->borrowed_at)->format('d M Y') }}
                                                </td>
                                                <td>{{ $borrow->duration }} hari</td>
                                                <td>
                                                    @if ($borrow->confirmation)
                                                        <span class="badge badge-success">Dikonfirmasi</span>
                                                    @else
                                                        <span class="badge badge-warning">Menunggu</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
                                <p class="text-muted">Belum ada peminjaman terbaru.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</x-admin-layout>
