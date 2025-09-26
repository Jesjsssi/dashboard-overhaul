@extends('template_admin.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-lg-12 mb-4 order-0">
                <div class="card">
                    <div class="d-flex align-items-end row">
                        <div class="col-12">
                            <div class="card-body">
                                <h5 class="card-title text-primary">Tambah Tahapan</h5>
                                <p class="mb-4">Tambah data tahapan baru</p>

                                <form action="{{ route('superadmin.master-tahapan.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="return_kategori" value="{{ $selectedKategori }}">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="kategori" class="form-label">Kategori <span
                                                        class="text-danger">*</span></label>
                                                <select class="form-select @error('kategori') is-invalid @enderror"
                                                    id="kategori" name="kategori" required>
                                                    <option value="">Pilih Kategori</option>
                                                    <option value="material" {{ (old('kategori', $selectedKategori) == 'material') ? 'selected' : '' }}>Material
                                                    </option>
                                                    <option value="jasa" {{ (old('kategori', $selectedKategori) == 'jasa') ? 'selected' : '' }}>Jasa</option>
                                                    <option value="eksekusi" {{ (old('kategori', $selectedKategori) == 'eksekusi') ? 'selected' : '' }}>Eksekusi
                                                    </option>
                                                </select>
                                                @error('kategori')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="step" class="form-label">Step <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('step') is-invalid @enderror"
                                                    id="step" name="step" value="{{ old('step') }}" required>
                                                @error('step')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Weight Factor -->
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="weight_factor" class="form-label">Weight Factor <span
                                                        class="text-danger">*</span></label>
                                                <input type="number" step="0.01" min="0" max="100"
                                                    class="form-control @error('weight_factor') is-invalid @enderror"
                                                    id="weight_factor" name="weight_factor"
                                                    value="{{ old('weight_factor') }}" required>
                                                <small class="text-muted">Masukkan nilai antara 0-100</small>
                                                @error('weight_factor')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- IRKAP -->
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="irkap" class="form-label">Tahapan IRKAP <span
                                                        class="text-danger">*</span></label>
                                                <select class="form-select @error('irkap') is-invalid @enderror" id="irkap"
                                                    name="irkap" required>
                                                    <option value="">Pilih Tahapan IRKAP</option>
                                                    @foreach(\App\Models\MasterTahapan::getTahapanList() as $key => $value)
                                                        <option value="{{ $key }}" {{ old('irkap') == $key ? 'selected' : '' }}>
                                                            {{ $key }} - {{ $value }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('irkap')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between mt-4">
                                        <a href="{{ route('superadmin.master-tahapan', ['kategori' => $selectedKategori]) }}"
                                            class="btn btn-secondary">
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
