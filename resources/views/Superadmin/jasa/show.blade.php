@extends('template_admin.layout')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Jasa /</span> Detail
    </h4>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <h5 class="card-header">Detail Jasa</h5>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <table class="table">
                                <tr>
                                    <th width="30%">Kode Jasa</th>
                                    <td>{{ $jasa->kode_jasa }}</td>
                                </tr>
                                <tr>
                                    <th>Judul Kontrak</th>
                                    <td>{{ $jasa->judul_kontrak }}</td>
                                </tr>
                                <tr>
                                    <th>Disiplin</th>
                                    <td>{{ $disiplin->remark }}</td>
                                </tr>

                                <tr>
                                    <th>Planner</th>
                                    <td>{{ $jasa->planner }}</td>
                                </tr>
                                <tr>
                                    <th>WO</th>
                                    <td>{{ $jasa->wo }}</td>
                                </tr>
                                <tr>
                                    <th>PR</th>
                                    <td>{{ $jasa->pr }}</td>
                                </tr>
                                <tr>
                                    <th>PO</th>
                                    <td>{{ $jasa->po }}</td>
                                </tr>
                                <tr>
                                    <th>Pemenang</th>
                                    <td>{{ $jasa->pemenang }}</td>
                                </tr>
                                <tr>
                                    <th>Keterangan</th>
                                    <td>{{ $jasa->keterangan }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="nav-align-top mb-4">
                                <ul class="nav nav-tabs nav-fill" role="tablist">
                                    <li class="nav-item">
                                        <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab"
                                            data-bs-target="#actual-tab">
                                            ACTUAL
                                        </button>
                                    </li>
                                    <li class="nav-item">
                                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                                            data-bs-target="#plan-tab">
                                            PLAN
                                        </button>
                                    </li>
                                </ul>

                                <div class="tab-content">
                                    <div class="tab-pane fade show active" id="actual-tab">
                                        <form action="{{ route('superadmin.jasa.update-actual', $jasa->id_jasa) }}"
                                            method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    @foreach($tahapans as $index => $tahapan)
                                                    <tr>
                                                        <th width="30%">{{ $tahapan->step }}</th>
                                                        <td>
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <label class="form-label mb-1">Tanggal Start</label>
                                                                    <input type="date"
                                                                        name="actual_start[{{ $tahapan->id }}]"
                                                                        class="form-control"
                                                                        value="{{ isset($details[$tahapan->id]) && $details[$tahapan->id]->actual_start ? date('Y-m-d', strtotime($details[$tahapan->id]->actual_start)) : '' }}"
                                                                        {{ $index > 0 && isset($details[$tahapans[$index - 1]->id]) && $details[$tahapans[$index - 1]->id]->actual_progress < 100 ? 'disabled' : '' }}>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label class="form-label mb-1">Tanggal
                                                                        Finish</label>
                                                                    <input type="date"
                                                                        name="actual_finish[{{ $tahapan->id }}]"
                                                                        class="form-control"
                                                                        value="{{ isset($details[$tahapan->id]) && $details[$tahapan->id]->actual_finish ? date('Y-m-d', strtotime($details[$tahapan->id]->actual_finish)) : '' }}"
                                                                        {{ $index > 0 && isset($details[$tahapans[$index - 1]->id]) && $details[$tahapans[$index - 1]->id]->actual_progress < 100 ? 'disabled' : '' }}>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label class="form-label mb-1">Progress</label>
                                                                    <div class="input-group">
                                                                        <input type="number"
                                                                            name="actual_progress[{{ $tahapan->id }}]"
                                                                            class="form-control"
                                                                            value="{{ isset($details[$tahapan->id]) ? $details[$tahapan->id]->actual_progress : 0 }}"
                                                                            min="0" max="100" {{ $index > 0 && isset($details[$tahapans[$index - 1]->id]) && $details[$tahapans[$index - 1]->id]->actual_progress < 100 ? 'disabled' : '' }}>
                                                                        <span class="input-group-text">%</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </table>
                                            </div>
                                            <div class="mt-3">
                                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                            </div>
                                        </form>
                                    </div>

                                    <!-- Similarly modify the plan-tab section -->
                                    <div class="tab-pane fade" id="plan-tab">
                                        <form action="{{ route('superadmin.jasa.update-plan', $jasa->id_jasa) }}"
                                            method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    @foreach($tahapans as $index => $tahapan)
                                                    <tr>
                                                        <th width="30%">{{ $tahapan->step }}</th>
                                                        <td>
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <label class="form-label mb-1">Tanggal Start</label>
                                                                    <input type="date"
                                                                        name="plan_start[{{ $tahapan->id }}]"
                                                                        class="form-control"
                                                                        value="{{ isset($details[$tahapan->id]) && $details[$tahapan->id]->plan_start ? date('Y-m-d', strtotime($details[$tahapan->id]->plan_start)) : '' }}"
                                                                        {{ $index > 0 && isset($details[$tahapans[$index - 1]->id]) && $details[$tahapans[$index - 1]->id]->plan_progress < 100 ? 'disabled' : '' }}>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label class="form-label mb-1">Tanggal
                                                                        Finish</label>
                                                                    <input type="date"
                                                                        name="plan_finish[{{ $tahapan->id }}]"
                                                                        class="form-control"
                                                                        value="{{ isset($details[$tahapan->id]) && $details[$tahapan->id]->plan_finish ? date('Y-m-d', strtotime($details[$tahapan->id]->plan_finish)) : '' }}"
                                                                        {{ $index > 0 && isset($details[$tahapans[$index - 1]->id]) && $details[$tahapans[$index - 1]->id]->plan_progress < 100 ? 'disabled' : '' }}>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label class="form-label mb-1">Progress</label>
                                                                    <div class="input-group">
                                                                        <input type="number"
                                                                            name="plan_progress[{{ $tahapan->id }}]"
                                                                            class="form-control"
                                                                            value="{{ isset($details[$tahapan->id]) ? $details[$tahapan->id]->plan_progress : 0 }}"
                                                                            min="0" max="100" {{ $index > 0 && isset($details[$tahapans[$index - 1]->id]) && $details[$tahapans[$index - 1]->id]->plan_progress < 100 ? 'disabled' : '' }}>
                                                                        <span class="input-group-text">%</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </table>
                                            </div>
                                            <div class="mt-3">
                                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <a href="{{ route('superadmin.jasa') }}" class="btn btn-secondary">Kembali</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

   
    @endsection