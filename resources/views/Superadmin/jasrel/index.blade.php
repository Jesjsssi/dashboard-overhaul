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
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Jasrel /</span> Data Project</h4>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Daftar Project</h5>
                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('superadmin.jasrel') }}"
                        class="d-flex align-items-center gap-3">
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

                        <!-- Filter Kategori - Only show Jasa and Material dan Jasa -->
                        <div class="form-group" style="min-width: 200px;">
                            <select name="kategori" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="">-- Semua Kategori --</option>
                                <option value="Jasa" {{ request('kategori') == 'Jasa' ? 'selected' : '' }}>Jasa</option>
                                <option value="Material dan Jasa" {{ request('kategori') == 'Material dan Jasa' ? 'selected' : '' }}>Material dan Jasa</option>
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

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode RKAP</th>
                                    <th>Disiplin</th>
                                    <th>Sub Disiplin</th>
                                    <th>Tag No</th>
                                    <th>Program</th>
                                    <th>Weight Factor</th>
                                    <th>Step 1</th>
                                    <th>Durasi 1-2</th>
                                    <th>Step 2</th>
                                    <th>Durasi 2-3</th>
                                    <th>Step 3</th>
                                    <th>Durasi 3-4</th>
                                    <th>Step 4</th>
                                    <th>Durasi 4-5</th>
                                    <th>Step 5</th>
                                    <th>Durasi 5-6</th>
                                    <th>Step 6</th>
                                    <th>Durasi 6-7</th>
                                    <th>Step 7</th>
                                    <th>Durasi 7-8</th>
                                    <th>Step 8</th>
                                    <th>Durasi 8-9</th>
                                    <th>Step 9</th>
                                    <th>Durasi 9-10</th>
                                    <th>Step 10</th>
                                    <th>Durasi 10-11</th>
                                    <th>Step 11</th>
                                    <th>Durasi 11-12</th>
                                    <th>Step 12</th>
                                    <th>Durasi 12-13</th>
                                    <th>Step 13</th>
                                    <th>Durasi 13-14</th>
                                    <th>Step 14</th>
                                    <th>Durasi 14-15</th>
                                    <th>Step 15</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($projects as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item->kode_rkap }}</td>
                                    <td>{{ $item->disiplin->remark }}</td>
                                    <td>{{ $item->subDisiplin->remark ?? '-' }}</td>
                                    <td>{{ $item->tagno }}</td>
                                    <td>{{ $item->remark }}</td>
                                    <td>{{ number_format($item->weight_factor, 2) }}</td>
                                    <td>{{ $item->step_1_date ? date('d/m/Y', strtotime($item->step_1_date)) : '-' }}</td>
                                    
                                    <!-- Durasi Step 1-2 -->
                                    <td>
                                        @if($item->step_1_date && $item->step_2_date)
                                            @php
                                                $start = \Carbon\Carbon::parse($item->step_1_date);
                                                $end = \Carbon\Carbon::parse($item->step_2_date);
                                                $days = $end->diffInDays($start);
                                            @endphp
                                            {{ $days }} hari
                                        @else
                                            -
                                        @endif
                                    </td>
                                    
                                    <td>{{ $item->step_2_date ? date('d/m/Y', strtotime($item->step_2_date)) : '-' }}</td>
                                    
                                    <!-- Durasi Step 2-3 -->
                                    <td>
                                        @if($item->step_2_date && $item->step_3_date)
                                            @php
                                                $start = \Carbon\Carbon::parse($item->step_2_date);
                                                $end = \Carbon\Carbon::parse($item->step_3_date);
                                                $days = $end->diffInDays($start);
                                            @endphp
                                            {{ $days }} hari
                                        @else
                                            -
                                        @endif
                                    </td>
                                    
                                    <td>{{ $item->step_3_date ? date('d/m/Y', strtotime($item->step_3_date)) : '-' }}</td>
                                    
                                    <!-- Durasi Step 3-4 -->
                                    <td>
                                        @if($item->step_3_date && $item->step_4_date)
                                            @php
                                                $start = \Carbon\Carbon::parse($item->step_3_date);
                                                $end = \Carbon\Carbon::parse($item->step_4_date);
                                                $days = $end->diffInDays($start);
                                            @endphp
                                            {{ $days }} hari
                                        @else
                                            -
                                        @endif
                                    </td>
                                    
                                    <td>{{ $item->step_4_date ? date('d/m/Y', strtotime($item->step_4_date)) : '-' }}</td>
                                    
                                    <!-- Durasi Step 4-5 -->
                                    <td>
                                        @if($item->step_4_date && $item->step_5_date)
                                            @php
                                                $start = \Carbon\Carbon::parse($item->step_4_date);
                                                $end = \Carbon\Carbon::parse($item->step_5_date);
                                                $days = $end->diffInDays($start);
                                            @endphp
                                            {{ $days }} hari
                                        @else
                                            -
                                        @endif
                                    </td>
                                    
                                    <td>{{ $item->step_5_date ? date('d/m/Y', strtotime($item->step_5_date)) : '-' }}</td>
                                    
                                    <!-- Continue with the same pattern for other steps -->
                                    <td>
                                        @if($item->step_5_date && $item->step_6_date)
                                            @php
                                                $start = \Carbon\Carbon::parse($item->step_5_date);
                                                $end = \Carbon\Carbon::parse($item->step_6_date);
                                                $days = $end->diffInDays($start);
                                            @endphp
                                            {{ $days }} hari
                                        @else
                                            -
                                        @endif
                                    </td>
                                    
                                    <td>{{ $item->step_6_date ? date('d/m/Y', strtotime($item->step_6_date)) : '-' }}</td>
                                    
                                    <td>
                                        @if($item->step_6_date && $item->step_7_date)
                                            @php
                                                $start = \Carbon\Carbon::parse($item->step_6_date);
                                                $end = \Carbon\Carbon::parse($item->step_7_date);
                                                $days = $end->diffInDays($start);
                                            @endphp
                                            {{ $days }} hari
                                        @else
                                            -
                                        @endif
                                    </td>
                                    
                                    <td>{{ $item->step_7_date ? date('d/m/Y', strtotime($item->step_7_date)) : '-' }}</td>
                                    
                                    <td>
                                        @if($item->step_7_date && $item->step_8_date)
                                            @php
                                                $start = \Carbon\Carbon::parse($item->step_7_date);
                                                $end = \Carbon\Carbon::parse($item->step_8_date);
                                                $days = $end->diffInDays($start);
                                            @endphp
                                            {{ $days }} hari
                                        @else
                                            -
                                        @endif
                                    </td>
                                    
                                    <td>{{ $item->step_8_date ? date('d/m/Y', strtotime($item->step_8_date)) : '-' }}</td>
                                    
                                    <td>
                                        @if($item->step_8_date && $item->step_9_date)
                                            @php
                                                $start = \Carbon\Carbon::parse($item->step_8_date);
                                                $end = \Carbon\Carbon::parse($item->step_9_date);
                                                $days = $end->diffInDays($start);
                                            @endphp
                                            {{ $days }} hari
                                        @else
                                            -
                                        @endif
                                    </td>
                                    
                                    <td>{{ $item->step_9_date ? date('d/m/Y', strtotime($item->step_9_date)) : '-' }}</td>
                                    
                                    <td>
                                        @if($item->step_9_date && $item->step_10_date)
                                            @php
                                                $start = \Carbon\Carbon::parse($item->step_9_date);
                                                $end = \Carbon\Carbon::parse($item->step_10_date);
                                                $days = $end->diffInDays($start);
                                            @endphp
                                            {{ $days }} hari
                                        @else
                                            -
                                        @endif
                                    </td>
                                    
                                    <td>{{ $item->step_10_date ? date('d/m/Y', strtotime($item->step_10_date)) : '-' }}</td>
                                    
                                    <td>
                                        @if($item->step_10_date && $item->step_11_date)
                                            @php
                                                $start = \Carbon\Carbon::parse($item->step_10_date);
                                                $end = \Carbon\Carbon::parse($item->step_11_date);
                                                $days = $end->diffInDays($start);
                                            @endphp
                                            {{ $days }} hari
                                        @else
                                            -
                                        @endif
                                    </td>
                                    
                                    <td>{{ $item->step_11_date ? date('d/m/Y', strtotime($item->step_11_date)) : '-' }}</td>
                                    
                                    <td>
                                        @if($item->step_11_date && $item->step_12_date)
                                            @php
                                                $start = \Carbon\Carbon::parse($item->step_11_date);
                                                $end = \Carbon\Carbon::parse($item->step_12_date);
                                                $days = $end->diffInDays($start);
                                            @endphp
                                            {{ $days }} hari
                                        @else
                                            -
                                        @endif
                                    </td>
                                    
                                    <td>{{ $item->step_12_date ? date('d/m/Y', strtotime($item->step_12_date)) : '-' }}</td>
                                    
                                    <td>
                                        @if($item->step_12_date && $item->step_13_date)
                                            @php
                                                $start = \Carbon\Carbon::parse($item->step_12_date);
                                                $end = \Carbon\Carbon::parse($item->step_13_date);
                                                $days = $end->diffInDays($start);
                                            @endphp
                                            {{ $days }} hari
                                        @else
                                            -
                                        @endif
                                    </td>
                                    
                                    <td>{{ $item->step_13_date ? date('d/m/Y', strtotime($item->step_13_date)) : '-' }}</td>
                                    
                                    <td>
                                        @if($item->step_13_date && $item->step_14_date)
                                            @php
                                                $start = \Carbon\Carbon::parse($item->step_13_date);
                                                $end = \Carbon\Carbon::parse($item->step_14_date);
                                                $days = $end->diffInDays($start);
                                            @endphp
                                            {{ $days }} hari
                                        @else
                                            -
                                        @endif
                                    </td>
                                    
                                    <td>{{ $item->step_14_date ? date('d/m/Y', strtotime($item->step_14_date)) : '-' }}</td>
                                    
                                    <td>
                                        @if($item->step_14_date && $item->step_15_date)
                                            @php
                                                $start = \Carbon\Carbon::parse($item->step_14_date);
                                                $end = \Carbon\Carbon::parse($item->step_15_date);
                                                $days = $end->diffInDays($start);
                                            @endphp
                                            {{ $days }} hari
                                        @else
                                            -
                                        @endif
                                    </td>
                                    
                                    <td>{{ $item->step_15_date ? date('d/m/Y', strtotime($item->step_15_date)) : '-' }}</td>
                                    
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('superadmin.project.edit', $item->id) }}"
                                                class="btn btn-sm btn-warning">
                                                <i class="bx bx-edit"></i>
                                            </a>
                                            <form action="{{ route('superadmin.project.destroy', $item->id) }}"
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
                                    <td colspan="37" class="text-center py-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="bx bx-folder-open mb-2" style="font-size: 3rem;"></i>
                                            <h5>Belum ada data project</h5>
                                            <p class="text-muted">Silakan tambahkan data project terlebih dahulu</p>
                                            <a href="{{ route('superadmin.project.create') }}" class="btn btn-primary">
                                                <i class="bx bx-plus"></i> Tambah Project
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $projects->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection