@extends('template_admin.layout')

@section('content')
 <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master Jenis Pengadaan /</span> Data Master Jenis Pengadaan</h4>

    <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Daftar Master Sub Disiplin</h5>
                        <a href="{{ route('superadmin.master-sub-disiplin.create') }}" class="btn btn-primary">
                            <i class="bx bx-plus"></i> Tambah Master Sub Disiplin
                        </a>
                    </div>

                            <div class="card-body">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Disiplin</th>
                                             <th>Sub Disiplin</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($subDisiplin as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $item->disiplin->remark ?? '-' }}</td>
                                            <td>{{ $item->remark }}</td>
                                            <td>
                                                <a href="{{ route('superadmin.master-sub-disiplin.edit', $item->id_sub_disiplin) }}" 
                                                   class="btn btn-warning btn-sm">
                                                    <i class="bx bx-edit"></i>
                                                </a>
                                                <form action="{{ route('superadmin.master-sub-disiplin.destroy', $item->id_sub_disiplin) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" 
                                                            onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center">Tidak ada data</td>
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
    </div>
</div>
@endsection
