<x-admin-layout title="Dashboard Pustakawan">
    {{-- Welcome Section --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Selamat Datang, Pustakawan
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ Auth::user()->name }}
                            </div>
                            <div class="text-muted small mt-1">
                                Kelola operasional perpustakaan: konfirmasi peminjaman dan pengembalian buku.
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-tie fa-3x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Pending Tasks (Priority Actions) --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-left-warning shadow">
                <div class="card-header py-3 bg-warning">
                    <h6 class="m-0 font-weight-bold text-white">
                        <i class="fas fa-tasks mr-2"></i>Tugas yang Perlu Dikerjakan
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ dynamic_route('borrows.index') }}" class="text-decoration-none">
                                <div class="card border-left-warning h-100">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                    Peminjaman Pending
                                                </div>
                                                <div class="h3 mb-0 font-weight-bold text-gray-800">
                                                    {{ $pending_borrow_confirmations }}
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-clipboard-check fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-3 mb-3">
                            <a href="{{ dynamic_route('returns.index') }}" class="text-decoration-none">
                                <div class="card border-left-info h-100">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                    Pengembalian Pending
                                                </div>
                                                <div class="h3 mb-0 font-weight-bold text-gray-800">
                                                    {{ $pending_return_confirmations }}
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-undo fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-3 mb-3">
                            <a href="{{ dynamic_route('returns.index') }}" class="text-decoration-none">
                                <div class="card border-left-danger h-100">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                                    Terlambat (Proses Denda)
                                                </div>
                                                <div class="h3 mb-0 font-weight-bold text-gray-800">
                                                    {{ $overdue_to_process }}
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-3 mb-3">
                            <a href="{{ dynamic_route('returns.index') }}" class="text-decoration-none">
                                <div class="card border-left-dark h-100">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                                                    Denda Belum Dibayar
                                                </div>
                                                <div class="h3 mb-0 font-weight-bold text-gray-800">
                                                    {{ $fines_to_collect }}
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="row mb-3">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Buku
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $total_books }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-book fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Buku Tersedia
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $available_books }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Member
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $total_members }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Peminjaman Aktif
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $active_borrows }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Today's Activity --}}
    <div class="row mb-4">
        <div class="col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Peminjaman Hari Ini
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $borrows_today }} peminjaman
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Pengembalian Hari Ini
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $returns_today }} pengembalian
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Aksi Cepat</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ dynamic_route('members.index') }}" class="btn btn-outline-success btn-block py-3">
                                <i class="fas fa-users fa-2x d-block mb-2"></i>
                                <strong>Kelola Member</strong>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ dynamic_route('books.index') }}" class="btn btn-outline-info btn-block py-3">
                                <i class="fas fa-book fa-2x d-block mb-2"></i>
                                <strong>Kelola Buku</strong>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ dynamic_route('borrows.index') }}" class="btn btn-outline-warning btn-block py-3">
                                <i class="fas fa-clipboard-list fa-2x d-block mb-2"></i>
                                <strong>Kelola Peminjaman</strong>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ dynamic_route('returns.index') }}" class="btn btn-outline-danger btn-block py-3">
                                <i class="fas fa-undo fa-2x d-block mb-2"></i>
                                <strong>Kelola Pengembalian</strong>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Pending Activities --}}
    <div class="row">
        <div class="col-xl-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 bg-warning">
                    <h6 class="m-0 font-weight-bold text-white">Peminjaman Perlu Dikonfirmasi</h6>
                </div>
                <div class="card-body">
                    @if($recent_pending_borrows->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Peminjam</th>
                                        <th>Buku</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recent_pending_borrows as $borrow)
                                        <tr>
                                            <td>{{ $borrow->user->name }}</td>
                                            <td>{{ Str::limit($borrow->book->title, 20) }}</td>
                                            <td>
                                                <a href="{{ dynamic_route('borrows.edit', $borrow) }}" class="btn btn-sm btn-warning">
                                                    Konfirmasi
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-center text-muted py-3">Tidak ada peminjaman pending</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 bg-info">
                    <h6 class="m-0 font-weight-bold text-white">Pengembalian Perlu Dikonfirmasi</h6>
                </div>
                <div class="card-body">
                    @if($recent_pending_returns->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Peminjam</th>
                                        <th>Buku</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recent_pending_returns as $return)
                                        <tr>
                                            <td>{{ $return->user->name }}</td>
                                            <td>{{ Str::limit($return->book->title, 20) }}</td>
                                            <td>
                                                <a href="{{ dynamic_route('returns.edit', $return) }}" class="btn btn-sm btn-info">
                                                    Proses
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-center text-muted py-3">Tidak ada pengembalian pending</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Books Need Restocking --}}
    @if($low_stock_books->count() > 0)
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card border-left-warning shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-warning">
                            <i class="fas fa-exclamation-triangle mr-2"></i>Buku Perlu Restocking
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Judul Buku</th>
                                        <th>Penulis</th>
                                        <th>Sisa Stok</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($low_stock_books as $book)
                                        <tr>
                                            <td>{{ $book->title }}</td>
                                            <td>{{ $book->writer }}</td>
                                            <td>
                                                <span class="badge badge-warning">{{ $book->amount }} buku</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</x-admin-layout>