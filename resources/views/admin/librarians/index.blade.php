<x-admin-layout title="List Pustakawan">
    <div class="card shadow mb-4">
        <div class="card-body">
            @if($success = session()->get('success'))
                <div class="card border-left-success">
                    <div class="card-body">{!! $success !!}</div>
                </div>
            @endif

            <a href="{{ route('admin.librarians.create') }}"
                class="btn btn-primary d-block d-sm-inline-block my-3">Tambah</a>

            <x-admin.search url="{{ route('admin.librarians.index') }}" placeholder="Cari pustakawan..." />

            <div class="table-responsive">
                <table class="table table-bordered" style="table-layout: fixed;">
                    <colgroup>
                        <col style="width: 80px;">     <!-- Foto -->
                        <col style="width: 25%;">      <!-- Nama -->
                        <col style="width: 15%;">      <!-- Tipe Nomor -->
                        <col style="width: 20%;">      <!-- Nomor -->
                        <col style="width: 20%;">      <!-- Telepon -->
                        <col style="width: 20%;">      <!-- Aksi -->
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="text-center">Foto</th>
                            <th>Nama</th>
                            <th>Tipe Nomor</th>
                            <th>Nomor</th>
                            <th>Telepon</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($librarians as $librarian)
                            <tr>
                                <td class="text-center align-middle">
                                    @if($librarian->photo)
                                        <img src="{{ asset('storage/' . $librarian->photo) }}" 
                                             alt="{{ $librarian->name }}" 
                                             class="rounded-circle"
                                             style="width: 50px; height: 50px; object-fit: cover;">
                                    @else
                                        <div class="rounded-circle bg-secondary d-inline-flex align-items-center justify-content-center text-white"
                                             style="width: 50px; height: 50px;">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    @endif
                                </td>
                                <td class="align-middle">
                                    <div class="text-truncate" title="{{ $librarian->name }}">
                                        {{ $librarian->name }}
                                    </div>
                                </td>
                                <td class="align-middle">{{ $librarian->number_type }}</td>
                                <td class="align-middle">{{ $librarian->number }}</td>
                                <td class="align-middle">+{{ $librarian->telephone }}</td>
                                <td class="align-middle">
                                    <a href="{{ route('admin.librarians.edit', $librarian) }}" 
                                       class="btn btn-link p-0 mx-1">Edit</a>

                                    <form action="{{ route('admin.librarians.destroy', $librarian) }}" method="POST"
                                        class="d-inline"
                                        onsubmit="return confirm('Anda yakin ingin menghapus pustakawan ini?')">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="btn btn-link text-danger p-0 mx-1">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-5">
                    {{ $librarians->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Fixed table layout for consistent column widths */
        .table {
            table-layout: fixed;
            width: 100%;
        }

        /* Prevent text overflow */
        .table td {
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        /* Responsive: switch to auto layout on mobile */
        @media (max-width: 768px) {
            .table {
                table-layout: auto;
            }
        }
    </style>
</x-admin-layout>