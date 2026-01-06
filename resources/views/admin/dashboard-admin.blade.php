<x-admin-layout title="Dashboard Admin">
    {{-- Welcome Section --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Selamat Datang, Administrator
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ Auth::user()->name }}
                            </div>
                            <div class="text-muted small mt-1">
                                Anda memiliki akses penuh ke seluruh sistem perpustakaan.
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-shield fa-3x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistics Cards Row 1: Books --}}
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
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Stok Menipis
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $low_stock_books->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Buku Habis
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $out_of_stock_books }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistics Cards Row 2: Users --}}
    <div class="row mb-3">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Pengguna
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $total_users }}
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
            <div class="card border-left-dark shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                                Admin
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $total_admins }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-shield fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                Pustakawan
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $total_librarians }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-tie fa-2x text-gray-300"></i>
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
                                Member
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $total_members }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistics Cards Row 3: Borrows & Returns --}}
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Peminjaman Pending
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $pending_borrows }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
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
                                Total Peminjaman
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $total_borrows }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Pengembalian Terlambat
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $overdue_returns }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-circle fa-2x text-gray-300"></i>
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
                                Denda Belum Dibayar
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $unpaid_fines }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions for Admin --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Aksi Cepat Admin</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ dynamic_route('librarians.index') }}" class="btn btn-outline-primary btn-block py-3">
                                <i class="fas fa-user-tie fa-2x d-block mb-2"></i>
                                <strong>Kelola Pustakawan</strong>
                            </a>
                        </div>
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
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Activities --}}
    <div class="row">
        <div class="col-xl-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Peminjaman Terbaru</h6>
                </div>
                <div class="card-body">
                    @if($recent_borrows->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Peminjam</th>
                                        <th>Buku</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recent_borrows as $borrow)
                                        <tr>
                                            <td>{{ $borrow->user->name }}</td>
                                            <td>{{ Str::limit($borrow->book->title, 25) }}</td>
                                            <td>
                                                @if($borrow->confirmation)
                                                    <span class="badge badge-success">Dikonfirmasi</span>
                                                @else
                                                    <span class="badge badge-warning">Pending</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-center text-muted py-3">Belum ada peminjaman</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Buku Stok Menipis</h6>
                </div>
                <div class="card-body">
                    @if($low_stock_books->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Judul Buku</th>
                                        <th>Sisa Stok</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($low_stock_books as $book)
                                        <tr>
                                            <td>{{ Str::limit($book->title, 30) }}</td>
                                            <td>
                                                <span class="badge badge-warning">{{ $book->amount }} buku</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-center text-muted py-3">Semua buku stok aman</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>