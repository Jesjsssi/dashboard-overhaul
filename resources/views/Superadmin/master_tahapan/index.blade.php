@extends('template_admin.layout')

@section('content')

    <style>
        .filter-container {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .filter-item {
            flex: 1;
            min-width: 200px;
        }

        @media (max-width: 768px) {
            .filter-container {
                flex-direction: column;
            }

            .filter-item {
                width: 100%;
            }
        }

        .btn-urutan {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            margin: 0 2px;
        }
    </style>

    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master Tahapan /</span> Data Master Tahapan</h4>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Daftar Master Tahapan</h5>

                        <!-- Filter Form -->
                        <form method="GET" action="{{ route('superadmin.master-tahapan') }}"
                            class="d-flex align-items-center gap-3">
                            <!-- Filter Kategori -->
                            <div class="form-group" style="min-width: 200px;">
                                <select name="kategori" id="filter_kategori" class="form-select form-select-sm">
                                    <option value="">-- Semua Kategori --</option>
                                    @foreach($kategori as $kat)
                                        <option value="{{ $kat->kategori }}" {{ $selectedKategori == $kat->kategori ? 'selected' : '' }}>
                                            {{ $kat->kategori }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Search Box -->
                            <div class="form-group" style="min-width: 200px;">
                                <div class="input-group input-group-sm">
                                    <input type="text" name="search" class="form-control"
                                        placeholder="Cari Kategori/Step/Weight Factor/No Urut..."
                                        value="{{ request('search') }}">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bx bx-filter-alt"></i> Filter
                                    </button>
                                </div>
                            </div>

                        </form>
                        <a href="{{ route('superadmin.master-tahapan.create', ['kategori' => $selectedKategori]) }}" class="btn btn-primary">
                            <i class="bx bx-plus"></i> Tambah Master Tahapan
                        </a>
                    </div>

                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kategori</th>
                                    <th>Tahapan IRKAP</th>
                                    <th>Step</th>
                                    <th>Weight Factor</th>
                                    <th>Urutan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tahapan as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item->kategori }}</td>
                                        <td>{{ $item->irkap}}</td>
                                        <td>{{ $item->step }}</td>
                                        <td>{{ $item->weight_factor }}</td>
                                        <td class="text-center">
                                            <div class="d-flex align-items-center justify-content-center">
                                                <form action="{{ route('superadmin.master-tahapan.move-up', $item->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="kategori" value="{{ request('kategori') }}">
                                                    <button type="submit" class="btn btn-sm btn-info btn-urutan" {{ $item->urutan <= 1 ? 'disabled' : '' }}>
                                                        <i class="bx bx-up-arrow-alt"></i>
                                                    </button>
                                                </form>
                                                <span class="mx-2">{{ $item->urutan }}</span>
                                                <form action="{{ route('superadmin.master-tahapan.move-down', $item->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="kategori" value="{{ request('kategori') }}">
                                                    <button type="submit" class="btn btn-sm btn-info btn-urutan">
                                                        <i class="bx bx-down-arrow-alt"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="{{ route('superadmin.master-tahapan.edit', ['id' => $item->id, 'kategori' => request('kategori')]) }}"
                                                class="btn btn-warning btn-sm">
                                                <i class="bx bx-edit"></i>
                                            </a>
                                            <form action="{{ route('superadmin.master-tahapan.destroy', $item->id) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="kategori" value="{{ request('kategori') }}">
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Tidak ada data</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
