@extends('template_admin.layout')

@section('title', 'Import Data Project')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Import Data Project</h5>
                    <a href="{{ route('superadmin.project') }}" class="btn btn-secondary">
                        <i class="bx bx-arrow-back"></i> Kembali
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    @if(session('error'))
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    @if($errors->any())
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Upload File Excel</h6>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('superadmin.import-data.import') }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="id_eps" class="form-label">EPS <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select @error('id_eps') is-invalid @enderror"
                                                id="id_eps" name="id_eps" required>
                                                <option value="">Pilih EPS</option>
                                                @foreach($eps_list as $eps)
                                                <option value="{{ $eps->id_eps }}"
                                                    {{ old('id_eps') == $eps->id_eps ? 'selected' : '' }}
                                                    {{ !old('id_eps') && $defaultEps && $eps->id_eps == $defaultEps->id_eps ? 'selected' : '' }}>
                                                    {{ $eps->jenis_project }} - {{ $eps->tahun }} -
                                                    {{ $eps->remark }}{{ $eps->default ? ' (Default)' : '' }}
                                                </option>
                                                @endforeach
                                            </select>
                                            @error('id_eps')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="file" class="form-label">File Excel <span
                                                    class="text-danger">*</span></label>
                                            <input type="file" class="form-control @error('file') is-invalid @enderror"
                                                id="file" name="file" accept=".xlsx,.xls,.csv" required>
                                            <div class="form-text">Format yang didukung: .xlsx, .xls, .csv (Maksimal
                                                5MB)</div>
                                            @error('file')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <button type="submit" class="btn btn-primary">
                                            <i class="bx bx-upload"></i> Import Data
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Template Excel</h6>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted">Download template Excel untuk memastikan format data yang
                                        benar.</p>

                                    <div class="mb-3">
                                        <a href="{{ route('superadmin.download-template') }}" class="btn btn-success">
                                            <i class="bx bx-download"></i> Download Template Excel
                                        </a>
                                    </div>

                                    <div class="alert alert-info">
                                        <h6 class="alert-heading">Format Kolom yang Diperlukan:</h6>
                                        <ul class="mb-0">
                                            <li><strong>KODE RKAP</strong> - Kode RKAP (Wajib)</li>
                                            <li><strong>Tagno</strong> - Tag Number (Wajib)</li>
                                            <li><strong>Program</strong> - Program (Opsional)</li>
                                            <li><strong>Disiplin</strong>:
                                                <ul>
                                                    <li>Isi dengan nama disiplin atau ID disiplin (Opsional)</li>
                                                    <li>Untuk Electrical & Instrument, format yang didukung:
                                                        <ul>
                                                            <li>Elec. & Inst.</li>
                                                            <li>Electrical & Instrument</li>
                                                            <li>Elec & Inst</li>
                                                            <li>Electrical/Instrument</li>
                                                            <li>Dan variasi format lainnya akan dikonversi otomatis</li>
                                                        </ul>
                                                    </li>
                                                    <li>Jika kosong, akan menggunakan disiplin default</li>
                                                </ul>
                                            </li>
                                            <li><strong>Kategori</strong> - Material, Jasa, atau Material dan jasa (Opsional)
                                            </li>
                                        </ul>
                                    </div>

                                    <div class="alert alert-warning">
                                        <h6 class="alert-heading">Panduan Penggunaan:</h6>
                                        <ul class="mb-0">
                                            <li><strong>KODE RKAP</strong>:
                                                <ul>
                                                    <li>Wajib diisi</li>
                                                    <li>Harus unik dalam satu EPS</li>
                                                    <li>Akan menjadi identifikasi utama project</li>
                                                </ul>
                                            </li>
                                            <li><strong>Tagno</strong>:
                                                <ul>
                                                    <li>Boleh dikosongkan</li>
                                                    <li>Jika diisi, harus berupa teks</li>
                                                    <li>Tidak perlu unik</li>
                                                </ul>
                                            </li>
                                            <li><strong>Program</strong>: Isi dengan nama program project (opsional)
                                            </li>
                                            <li><strong>Disiplin</strong>: Format akan dinormalisasi secara otomatis
                                            </li>
                                            <li><strong>Kategori</strong>:
                                                <ul>
                                                    <li>Boleh dikosongkan</li>
                                                    <li>Nilai yang diterima: Material, Jasa, atau Material dan jasa
                                                    </li>
                                                    <li>Sistem akan menormalisasi berbagai variasi penulisan</li>
                                                </ul>
                                            </li>
                                        </ul>
                                        <hr>
                                        <p class="mb-0"><strong>Catatan:</strong>
                                        <ul>
                                            <li>Yang wajib diisi hanya kolom KODE RKAP dan Tag No</li>
                                            <li>Kolom lainnya bersifat opsional dan bisa dikosongkan</li>
                                            <li>Sistem akan menggunakan nilai default untuk kolom yang kosong</li>
                                        </ul>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection