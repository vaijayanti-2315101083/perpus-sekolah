<x-admin-layout title="Log Aktivitas">
    {{-- Filters --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Log Aktivitas</h6>
        </div>
        <div class="card-body">
            <form action="" method="GET" class="row g-3">
                <div class="col-md-2">
                    <label class="form-label small">Aksi</label>
                    <select name="action" class="form-control form-control-sm">
                        <option value="">Semua Aksi</option>
                        @foreach($actions as $action)
                            <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                                {{ ucfirst($action) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Tipe Model</label>
                    <select name="model_type" class="form-control form-control-sm">
                        <option value="">Semua Model</option>
                        @foreach($modelTypes as $type)
                            <option value="{{ $type }}" {{ request('model_type') == $type ? 'selected' : '' }}>
                                {{ $type }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Dari Tanggal</label>
                    <input type="date" name="start_date" class="form-control form-control-sm" 
                           value="{{ request('start_date') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Sampai Tanggal</label>
                    <input type="date" name="end_date" class="form-control form-control-sm" 
                           value="{{ request('end_date') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Cari</label>
                    <input type="text" name="search" class="form-control form-control-sm" 
                           placeholder="Cari deskripsi..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-sm mr-2">
                        <i class="fas fa-search"></i> Filter
                    </button>
                    <a href="{{ dynamic_route('activity-logs.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-undo"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Activity Logs Table --}}
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-history"></i> Riwayat Aktivitas
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th width="150">Waktu</th>
                            <th width="150">User</th>
                            <th width="100">Aksi</th>
                            <th width="100">Model</th>
                            <th>Deskripsi</th>
                            <th width="120">IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td>
                                    <small>{{ $log->created_at->format('d M Y') }}</small><br>
                                    <small class="text-muted">{{ $log->created_at->format('H:i:s') }}</small>
                                </td>
                                <td>
                                    @if($log->user)
                                        {{ $log->user->name }}
                                    @else
                                        <span class="text-muted">System</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $badgeClass = match($log->action) {
                                            'create' => 'success',
                                            'update' => 'warning',
                                            'delete' => 'danger',
                                            'login' => 'info',
                                            'logout' => 'secondary',
                                            default => 'primary',
                                        };
                                    @endphp
                                    <span class="badge badge-{{ $badgeClass }}">
                                        {{ ucfirst($log->action) }}
                                    </span>
                                </td>
                                <td>
                                    @if($log->model_type)
                                        <span class="badge badge-light">{{ $log->model_type }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ Str::limit($log->description, 60) }}</td>
                                <td><small class="text-muted">{{ $log->ip_address }}</small></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p>Belum ada log aktivitas</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="d-flex justify-content-end mt-3">
                {{ $logs->withQueryString()->links() }}
            </div>
        </div>
    </div>
</x-admin-layout>
