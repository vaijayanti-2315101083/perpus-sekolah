<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Perpustakaan</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 18px;
            margin-bottom: 5px;
        }
        .header p {
            font-size: 11px;
            color: #666;
        }
        .meta {
            margin-bottom: 20px;
            padding: 10px;
            background: #f5f5f5;
            border-radius: 5px;
        }
        .meta p {
            margin-bottom: 3px;
        }
        .summary-grid {
            margin-bottom: 25px;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }
        .summary-table td {
            padding: 8px 10px;
            border: 1px solid #ddd;
            width: 25%;
        }
        .summary-label {
            font-size: 9px;
            color: #666;
            text-transform: uppercase;
        }
        .summary-value {
            font-size: 14px;
            font-weight: bold;
            color: #333;
        }
        .section-title {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 10px;
            padding: 8px;
            background: #4e73df;
            color: white;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        table.data-table th,
        table.data-table td {
            border: 1px solid #ddd;
            padding: 6px 8px;
            text-align: left;
        }
        table.data-table th {
            background: #f8f9fc;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
        }
        table.data-table td {
            font-size: 9px;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }
        .badge-warning { background: #f6c23e; color: #333; }
        .badge-primary { background: #4e73df; color: white; }
        .badge-info { background: #36b9cc; color: white; }
        .badge-success { background: #1cc88a; color: white; }
        .badge-danger { background: #e74a3b; color: white; }
        .text-danger { color: #e74a3b; }
        .text-muted { color: #666; }
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 9px;
            color: #666;
        }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN PERPUSTAKAAN</h1>
        <p>Sistem Informasi Perpustakaan Sekolah</p>
    </div>

    <div class="meta">
        <p><strong>Periode:</strong> 
            @if($hasDateFilter)
                {{ $startDate ? $startDate->format('d/m/Y') : 'Awal' }} 
                s/d 
                {{ $endDate ? $endDate->format('d/m/Y') : 'Sekarang' }}
            @else
                Semua Data
            @endif
        </p>
        <p><strong>Dicetak:</strong> {{ $generatedAt->format('d/m/Y H:i') }}</p>
    </div>

    <!-- Summary -->
    <div class="summary-grid">
        <table class="summary-table">
            <tr>
                <td>
                    <div class="summary-label">Total Peminjaman</div>
                    <div class="summary-value">{{ $summary['total_borrows'] }}</div>
                </td>
                <td>
                    <div class="summary-label">Total Pengembalian</div>
                    <div class="summary-value">{{ $summary['total_returns'] }}</div>
                </td>
                <td>
                    <div class="summary-label">Denda Terkumpul</div>
                    <div class="summary-value">Rp {{ number_format($summary['fines_paid'], 0, ',', '.') }}</div>
                </td>
                <td>
                    <div class="summary-label">Denda Belum Dibayar</div>
                    <div class="summary-value">Rp {{ number_format($summary['fines_pending'], 0, ',', '.') }}</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Borrows Table -->
    <div class="section-title">
        Data Peminjaman ({{ $borrows->count() }})
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 15%">Tanggal</th>
                <th style="width: 25%">Peminjam</th>
                <th style="width: 35%">Judul Buku</th>
                <th style="width: 10%">Durasi</th>
                <th style="width: 10%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($borrows as $index => $borrow)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $borrow->borrowed_at->format('d/m/Y') }}</td>
                    <td>
                        {{ $borrow->user->name ?? '-' }}<br>
                        <small class="text-muted">{{ $borrow->user->number_type ?? '' }}: {{ $borrow->user->number ?? '-' }}</small>
                    </td>
                    <td>{{ $borrow->book->title ?? '-' }}</td>
                    <td>{{ $borrow->duration }} hari</td>
                    <td>
                        @switch($borrow->status)
                            @case('Pending')
                                <span class="badge badge-warning">Menunggu</span>
                                @break
                            @case('Borrowed')
                                <span class="badge badge-primary">Dipinjam</span>
                                @break
                            @case('Returning')
                                <span class="badge badge-info">Proses</span>
                                @break
                            @case('Returned')
                                <span class="badge badge-success">Selesai</span>
                                @break
                            @case('Overdue')
                                <span class="badge badge-danger">Terlambat</span>
                                @break
                            @default
                                <span class="badge">{{ $borrow->status }}</span>
                        @endswitch
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center;">Tidak ada data peminjaman</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Returns Table -->
    <div class="section-title">
        Data Pengembalian ({{ $returns->count() }})
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 15%">Tanggal</th>
                <th style="width: 25%">Peminjam</th>
                <th style="width: 30%">Judul Buku</th>
                <th style="width: 15%">Denda</th>
                <th style="width: 10%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($returns as $index => $return)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $return->returned_at ? \Carbon\Carbon::parse($return->returned_at)->format('d/m/Y') : '-' }}</td>
                    <td>
                        {{ $return->borrow->user->name ?? '-' }}<br>
                        <small class="text-muted">{{ $return->borrow->user->number_type ?? '' }}: {{ $return->borrow->user->number ?? '-' }}</small>
                    </td>
                    <td>{{ $return->borrow->book->title ?? '-' }}</td>
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
                    <td colspan="6" style="text-align: center;">Tidak ada data pengembalian</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Dokumen ini digenerate secara otomatis oleh Sistem Informasi Perpustakaan</p>
        <p>{{ $generatedAt->format('d F Y, H:i:s') }}</p>
    </div>
</body>
</html>
