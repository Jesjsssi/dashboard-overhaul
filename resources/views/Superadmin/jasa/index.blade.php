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

        .card-header .d-flex {
            flex-wrap: wrap;
            gap: 1rem;
        }

        .card-header form {
            flex: 1;
            min-width: 300px;
        }

        .card-header .d-flex.gap-2 {
            flex-shrink: 0;
        }

        @media (max-width: 992px) {
            .card-header .d-flex.justify-content-between {
                flex-direction: column;
                align-items: flex-start !important;
                gap: 1rem;
            }

            .card-header form {
                width: 100%;
                min-width: auto;
            }

            .card-header .d-flex.gap-2 {
                width: 100%;
                justify-content: flex-start;
            }
        }

        @media (max-width: 768px) {
            .filter-container {
                flex-direction: column;
            }

            .filter-item {
                width: 100%;
            }

            .card-header form {
                flex-direction: column;
                gap: 1rem !important;
            }

            .form-group {
                min-width: auto !important;
            }

            .card-header .d-flex.gap-2 {
                flex-direction: column;
                gap: 0.5rem !important;
            }

            .card-header .d-flex.gap-2 .btn {
                width: 100%;
                text-align: center;
            }
        }

        @media (max-width: 576px) {
            .card-header .mb-3 h5 {
                text-align: center;
                font-size: 1.1rem;
            }
        }
    </style>

    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Jasa /</span> Data Jasa</h4>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <!-- Title Section -->
                        <div class="mb-3">
                            <h5 class="mb-0">Daftar Jasa</h5>
                        </div>
                        
                        <!-- Filter and Action Section -->
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <!-- Filter Form -->
                            <form method="GET" action="{{ route('superadmin.jasa') }}" class="d-flex align-items-center gap-3 flex-wrap">
                                <!-- Filter EPS -->
                                <div class="form-group" style="min-width: 200px;">
                                    <select name="id_eps" id="filter_eps" class="form-select form-select-sm"
                                        onchange="this.form.submit()">
                                        @php
                                            $allEps = \App\Models\EPS::orderBy('tahun', 'desc')->orderBy('jenis_project')->get();
                                            $selectedEps = request('id_eps');
                                            $defaultEps = \App\Models\EPS::where('default', 1)->first();

                                            if (empty($selectedEps) && !request()->has('id_eps')) {
                                                $selectedEps = $defaultEps ? $defaultEps->id_eps : '';
                                            }
                                        @endphp
                                        <option value="">-- Semua EPS --</option>
                                        @foreach($allEps as $eps)
                                            <option value="{{ $eps->id_eps }}" {{ $selectedEps == $eps->id_eps ? 'selected' : '' }}>
                                                {{ $eps->jenis_project }} - {{ $eps->tahun }}{{ $eps->default ? ' (Default)' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Filter Disiplin -->
                                <div class="form-group" style="min-width: 200px;">
                                    <select name="id_disiplin" class="form-select form-select-sm" onchange="this.form.submit()">
                                        <option value="">-- Semua Disiplin --</option>
                                        @foreach(\App\Models\MasterDisiplin::all() as $disiplin)
                                            <option value="{{ $disiplin->id_disiplin }}" {{ request('id_disiplin') == $disiplin->id_disiplin ? 'selected' : '' }}>
                                                {{ $disiplin->remark }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Search Box -->
                                <div class="form-group" style="min-width: 200px;">
                                    <div class="input-group input-group-sm">
                                        <input type="text" name="search" class="form-control"
                                            placeholder="Cari RKAP/Tag No/Program..." value="{{ request('search') }}">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bx bx-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <!-- Action Buttons -->
                            <div class="d-flex gap-2 flex-wrap">
                                <a href="{{ route('superadmin.jasa.import') }}" class="btn btn-success">
                                    <i class="bx bx-upload"></i> Import
                                </a>
                                <a href="{{ route('superadmin.jasa.export', request()->only(['id_eps', 'id_disiplin', 'search'])) }}"
                                    class="btn btn-info">
                                    <i class="bx bx-download"></i> Export
                                </a>
                                <a href="{{ route('superadmin.jasa.create') }}" class="btn btn-primary">
                                    <i class="bx bx-plus"></i> Tambah Jasa
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if (session('import_errors') && count(session('import_errors')) > 0)
                            <div class="alert alert-warning alert-dismissible" role="alert">
                                <h6 class="alert-heading">Detail Error Import:</h6>
                                <ul class="mb-0">
                                    @foreach(session('import_errors') as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>EPS</th>
                                        <th>Kode Jasa</th>
                                        <th>Judul Kontrak</th>
                                        <th>Disiplin</th>
                                        <th>Planner</th>
                                        <th>WO</th>
                                        <th>PR</th>
                                        <th>PO</th>
                                        <th>Pemenang</th>
                                        <th>Keterangan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($jasas as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $item->eps->jenis_project }} - {{ $item->eps->tahun }}</td>
                                            <td>{{ $item->kode_jasa }}</td>
                                            <td>{{ $item->judul_kontrak }}</td>
                                            <td>{{ $item->disiplin->remark }}</td>
                                            <td>{{ $item->planner }}</td>
                                            <td>{{ $item->wo }}</td>
                                            <td>{{ $item->pr }}</td>
                                            <td>{{ $item->po }}</td>
                                            <td>{{ $item->pemenang }}</td>
                                            <td>{{ $item->keterangan }}</td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('superadmin.jasa.show', $item->id_jasa) }}"
                                                        class="btn btn-sm btn-info">
                                                        <i class="bx bx-show"></i>
                                                    </a>
                                                    <a href="{{ route('superadmin.jasa.edit', $item->id_jasa) }}"
                                                        class="btn btn-sm btn-warning">
                                                        <i class="bx bx-edit"></i>
                                                    </a>
                                                    <form action="{{ route('superadmin.jasa.destroy', $item->id_jasa) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">
                                                            <i class="bx bx-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="11" class="text-center py-4">
                                                <div class="d-flex flex-column align-items-center">
                                                    <i class="bx bx-folder-open mb-2" style="font-size: 3rem;"></i>
                                                    <h5>Belum ada data jasa</h5>
                                                    <p class="text-muted">Silakan tambahkan data jasa terlebih dahulu</p>
                                                    <a href="{{ route('superadmin.jasa.create') }}" class="btn btn-primary">
                                                        <i class="bx bx-plus"></i> Tambah Jasa
                                                    </a>
                                                </div>
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
    </div>
@endsection