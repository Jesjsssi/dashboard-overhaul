@extends('template_admin.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Master Disiplin /</span> Edit Master Disiplin
        </h4>

        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">Form Edit Master Disiplin</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('superadmin.master-disiplin.update', $master_disiplin->id_disiplin) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="remark">Remark</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control @error('remark') is-invalid @enderror"
                                        id="remark" name="remark" value="{{ old('remark', $master_disiplin->remark) }}"
                                        placeholder="Masukkan remark" />
                                    @error('remark')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row justify-content-end">
                                <div class="col-sm-10">
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                    <a href="{{ route('superadmin.master-disiplin') }}" class="btn btn-link">Kembali</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
