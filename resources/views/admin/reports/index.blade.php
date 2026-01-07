<x-admin-layout title="Laporan Perpustakaan">
    {{-- Date Filter --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter"></i> Filter Tanggal
            </h6>
            <a href="{{ dynamic_route('reports.export-pdf', request()->query()) }}" class="btn btn-danger btn-sm">
                <i class="fas fa-file-pdf"></i> Export PDF
            </a>
        </div>
        <div class="card-body">
            <form action="" method="GET" class="row align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Dari Tanggal</label>
                    <input type="date" name="start_date" class="form-control" 
                           value="{{ $startDate?->format('Y-m-d') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Sampai Tanggal</label>
                    <input type="date" name="end_date" class="form-control" 
                           value="{{ $endDate?->format('Y-m-d') }}">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filter
                    </button>
                    <a href="{{ dynamic_route('reports.index') }}" class="btn btn-secondary">
                        <i class="fas fa-undo"></i> Reset
                    </a>
                </div>
            </form>
            @if(isset($hasDateFilter) && $hasDateFilter)
                <div class="mt-2">
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i> 
                        Menampilkan data dari {{ $startDate?->format('d/m/Y') ?? 'awal' }} s/d {{ $endDate?->format('d/m/Y') ?? 'sekarang' }}
                    </small>
                </div>
            @else
                <div class="mt-2">
                    <small class="text-success">
                        <i class="fas fa-check-circle"></i> Menampilkan semua data
                    </small>
                </div>
            @endif
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Peminjaman
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $summary['total_borrows'] }}
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
                                Total Pengembalian
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $summary['total_returns'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-undo fa-2x text-gray-300"></i>
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
                                Denda Terkumpul
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp {{ number_format($summary['fines_paid'], 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
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
                                Rp {{ number_format($summary['fines_pending'], 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Transactions Tables --}}
    <div class="row">
        {{-- Borrows Table --}}
        <div class="col-xl-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-book"></i> Peminjaman ({{ $borrows->count() }})
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive" style="max-height: 400px;">
                        <table class="table table-sm table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Peminjam</th>
                                    <th>Buku</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($borrows as $borrow)
                                    <tr>
                                        <td><small>{{ $borrow->borrowed_at->format('d/m/Y') }}</small></td>
                                        <td>
                                            <strong>{{ $borrow->user->name ?? '-' }}</strong><br>
                                            <small class="text-muted">
                                                {{ $borrow->user->number_type ?? '' }}: {{ $borrow->user->number ?? '-' }}
                                            </small><br>
                                            <small class="text-info">
                                                <i class="fas fa-phone"></i> {{ $borrow->user->telephone ?? '-' }}
                                            </small>
                                        </td>
                                        <td>{{ Str::limit($borrow->book->title ?? '-', 20) }}</td>
                                        <td>
                                            @switch($borrow->status)
                                                @case('Pending')
                                                    <span class="badge badge-warning">{{ $borrow->statusLabel }}</span>
                                                    @break
                                                @case('Borrowed')
                                                    <span class="badge badge-primary">{{ $borrow->statusLabel }}</span>
                                                    @break
                                                @case('Returning')
                                                    <span class="badge badge-info">{{ $borrow->statusLabel }}</span>
                                                    @break
                                                @case('Returned')
                                                    <span class="badge badge-success">{{ $borrow->statusLabel }}</span>
                                                    @break
                                                @case('Overdue')
                                                    <span class="badge badge-danger">{{ $borrow->statusLabel }}</span>
                                                    @break
                                                @default
                                                    <span class="badge badge-secondary">{{ $borrow->statusLabel }}</span>
                                            @endswitch
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3">
                                            Tidak ada data
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Returns Table --}}
        <div class="col-xl-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-undo"></i> Pengembalian ({{ $returns->count() }})
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive" style="max-height: 400px;">
                        <table class="table table-sm table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Peminjam</th>
                                    <th>Denda</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($returns as $return)
                                    <tr>
                                        <td><small>{{ $return->returned_at ? \Carbon\Carbon::parse($return->returned_at)->format('d/m/Y') : '-' }}</small></td>
                                        <td>
                                            <strong>{{ $return->borrow->user->name ?? '-' }}</strong><br>
                                            <small class="text-muted">
                                                {{ $return->borrow->user->number_type ?? '' }}: {{ $return->borrow->user->number ?? '-' }}
                                            </small><br>
                                            <small class="text-info">
                                                <i class="fas fa-phone"></i> {{ $return->borrow->user->telephone ?? '-' }}
                                            </small>
                                        </td>
                                        <td>
                                            @if($return->fine > 0)
                                                <span class="text-danger">Rp {{ number_format($return->fine, 0, ',', '.') }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($return->is_paid)
                                                <span class="badge badge-success">Lunas</span>
                                            @else
                                                <span class="badge badge-danger">Belum</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3">
                                            Tidak ada data
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
