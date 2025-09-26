@extends('template_admin.layout')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-12 mb-4 order-0">
            <div class="card">
                <div class="d-flex align-items-end row">
                    <div class="col-12">
                        <div class="card-body">
                            <h5 class="card-title text-primary">Tambah Sub Disiplin</h5>
                            <p class="mb-4">Tambah data sub disiplin baru</p>

                            <form action="{{ route('superadmin.master-sub-disiplin.store') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="remark" class="form-label">Sub Disiplin</label>
                                            <input type="text" class="form-control @error('remark') is-invalid @enderror" 
                                                   id="remark" name="remark" value="{{ old('remark') }}" required>
                                            @error('remark')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="id_disiplin" class="form-label">Disiplin</label>
                                            <select class="form-select @error('id_disiplin') is-invalid @enderror" 
                                                    id="id_disiplin" name="id_disiplin" required>
                                                <option value="">Pilih Disiplin</option>
                                                @foreach($disiplins as $disiplin)
                                                    <option value="{{ $disiplin->id_disiplin }}" 
                                                            {{ old('id_disiplin') == $disiplin->id_disiplin ? 'selected' : '' }}>
                                                        {{ $disiplin->remark }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('id_disiplin')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('superadmin.master-sub-disiplin') }}" class="btn btn-secondary">
                                        <i class="bx bx-arrow-back"></i> Kembali
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bx bx-save"></i> Simpan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 