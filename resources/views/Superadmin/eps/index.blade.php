@extends('template_admin.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">EPS /</span> Data EPS</h4>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Daftar EPS</h5>
                        <a href="{{ route('superadmin.eps.create') }}" class="btn btn-primary">
                            <i class="bx bx-plus"></i> Tambah EPS
                        </a>
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
                                        <th>Tahun</th>
                                        <th>Project</th>
                                        <th>Keterangan</th>
                                        <th>Tanggal Shutdown</th>
                                        <th>Cut Off Date</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($epsList as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $item->tahun }}</td>
                                            <td>{{ $item->jenis_project }}</td>
                                            <td>
                                                <a href="{{ route('superadmin.project', ['id_eps' => $item->id_eps]) }}">
                                                    @if($item->default)
                                                        <b>{{ $item->remark }}</b>
                                                    @else
                                                        {{ $item->remark }}
                                                    @endif
                                                </a>
                                            </td>
                                            <td>
                                                @if(in_array($item->jenis_project, ['OH', 'Routine']))
                                                    {{ date('d-m-Y', strtotime('2025-01-01')) }}
                                                @else
                                                    {{ date('d-m-Y', strtotime($item->execution_date)) }}
                                                @endif
                                            </td>
                                            <td>{{ date('d-m-Y', strtotime($item->cutoff_date)) }}</td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('superadmin.eps.edit', $item->id_eps) }}"
                                                        class="btn btn-sm btn-warning">
                                                        <i class="bx bx-edit"></i>
                                                    </a>
                                                    <form action="{{ route('superadmin.eps.destroy', $item->id_eps) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">
                                                            <i class="bx bx-trash"></i>
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('superadmin.eps.setDefault', $item->id_eps) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-info" title="Jadikan Default" {{ $item->default ? 'disabled' : '' }}>
                                                            <i class="bx bx-bookmark{{ $item->default ? '-alt' : '' }}"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4">
                                                <div class="d-flex flex-column align-items-center">
                                                    <i class="bx bx-category mb-2" style="font-size: 3rem;"></i>
                                                    <h5>Belum ada data EPS</h5>
                                                    <p class="text-muted">Silakan tambahkan data EPS terlebih dahulu
                                                    </p>
                                                    <a href="{{ route('superadmin.eps.create') }}" class="btn btn-primary">
                                                        <i class="bx bx-plus"></i> Tambah EPS
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