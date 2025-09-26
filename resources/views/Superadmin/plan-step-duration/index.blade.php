@extends('template_admin.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Plan Step Duration /</span> Data Plan Step Duration</h4>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Daftar Plan Step Duration</h5>
                        <a href="{{ route('superadmin.plan-step-duration.create') }}" class="btn btn-primary">
                            <i class="bx bx-plus"></i> Tambah Plan Step Duration
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
                                        <th>Project</th>
                                        <th>Notif</th>
                                        <th>Rekomend</th>
                                        <th>Job Plan</th>
                                        <th>WO</th>
                                        <th>Mat Reser</th>
                                        <th>PR</th>
                                        <th>Tender</th>
                                        <th>PO</th>
                                        <th>GR</th>
                                        <th>GI</th>
                                        <th>Eksekusi</th>
                                        <th>Test Perfo</th>
                                        <th>SA</th>
                                        <th>Update OR</th>
                                        <th>Closed</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($planStepDuration as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ optional($item->project)->tagno ?? '-' }}</td>
                                            <td>{{ $item->notif }}</td>
                                            <td>{{ $item->rekomend }}</td>
                                            <td>{{ $item->job_plan }}</td>
                                            <td>{{ $item->wo }}</td>
                                            <td>{{ $item->mat_reser }}</td>
                                            <td>{{ $item->pr }}</td>
                                            <td>{{ $item->tender }}</td>
                                            <td>{{ $item->po }}</td>
                                            <td>{{ $item->gr }}</td>
                                            <td>{{ $item->gi }}</td>
                                            <td>{{ $item->eksekusi }}</td>
                                            <td>{{ $item->test_perfo }}</td>
                                            <td>{{ $item->sa }}</td>
                                            <td>{{ $item->update_or }}</td>
                                            <td>{{ $item->closed }}</td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('superadmin.plan-step-duration.edit', $item->id_plan_step_duration) }}"
                                                        class="btn btn-sm btn-warning">
                                                        <i class="bx bx-edit"></i>
                                                    </a>
                                                    <form action="{{ route('superadmin.plan-step-duration.destroy', $item->id_plan_step_duration) }}"
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
                                            <td colspan="18" class="text-center py-4">
                                                <div class="d-flex flex-column align-items-center">
                                                    <i class="bx bx-folder-open mb-2" style="font-size: 3rem;"></i>
                                                    <h5>Belum ada data plan step duration</h5>
                                                    <p class="text-muted">Silakan tambahkan data plan step duration terlebih dahulu</p>
                                                    <a href="{{ route('superadmin.plan-step-duration.create') }}" class="btn btn-primary">
                                                        <i class="bx bx-plus"></i> Tambah Plan Step Duration
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