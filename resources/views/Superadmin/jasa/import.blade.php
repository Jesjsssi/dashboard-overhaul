@extends('template_admin.layout')

@section('title', 'Import Data Jasa')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Import Data Jasa</h5>
                        <a href="{{ route('superadmin.jasa') }}" class="btn btn-secondary">
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
                                        <form action="{{ route('superadmin.jasa.import') }}" method="POST"
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
                                            <a href="{{ route('superadmin.jasa.download-template') }}"
                                                class="btn btn-success">
                                                <i class="bx bx-download"></i> Download Template Excel
                                            </a>
                                        </div>

                                        <div class="alert alert-info">
                                            <h6 class="alert-heading">Format Excel dengan 2 Sheet:</h6>

                                            <h6 class="mt-3">Sheet 1: Data Jasa</h6>
                                            <ul class="mb-0">
                                                <li><strong>Kode Jasa</strong> - Kode Jasa (Wajib)</li>
                                                <li><strong>Judul Kontrak</strong> - Judul Kontrak (Wajib)</li>
                                                <li><strong>Disiplin</strong> - Nama Disiplin (Wajib)</li>
                                                <li><strong>Planner</strong> - Nama Planner (Opsional)</li>
                                                <li><strong>WO</strong> - Work Order (Opsional)</li>
                                                <li><strong>PR</strong> - Purchase Request (Opsional)</li>
                                                <li><strong>PO</strong> - Purchase Order (Opsional)</li>
                                                <li><strong>Pemenang</strong> - Nama Vendor Pemenang (Opsional)</li>
                                                <li><strong>Keterangan</strong> - Keterangan Tambahan (Opsional)</li>
                                            </ul>

                                            <h6 class="mt-3">Sheet 2: Detail Progress</h6>
                                            <ul class="mb-0">
                                                <li><strong>Kode Jasa</strong> - Kode Jasa (Wajib, harus sama dengan Sheet 1)</li>
                                                <li><strong>Judul Kontrak / Step</strong> - Nama Step/Tahapan (Wajib)</li>
                                                <li><strong>Plan Start</strong> - Tanggal Mulai Rencana (Opsional, format: DD/MM/YYYY)</li>
                                                <li><strong>Plan Finish</strong> - Tanggal Selesai Rencana (Opsional, format: DD/MM/YYYY)</li>
                                                <li><strong>Actual Start</strong> - Tanggal Mulai Aktual (Opsional, format: DD/MM/YYYY)</li>
                                                <li><strong>Actual Finish</strong> - Tanggal Selesai Aktual (Opsional, format: DD/MM/YYYY)</li>
                                                <li><strong>Plan Progress</strong> - Progress Rencana dalam % (Opsional)</li>
                                                <li><strong>Actual Progress</strong> - Progress Aktual dalam % (Opsional)</li>
                                            </ul>
                                        </div>

                                        <div class="alert alert-warning">
                                            <h6 class="alert-heading">Panduan Penggunaan:</h6>
                                            <ul class="mb-0">
                                                <li><strong>EPS</strong>:
                                                    <ul>
                                                        <li>Wajib dipilih dari dropdown di atas</li>
                                                        <li>EPS menentukan kategori project untuk jasa</li>
                                                    </ul>
                                                </li>
                                                <li><strong>Kode Jasa</strong>:
                                                    <ul>
                                                        <li>Wajib diisi</li>
                                                        <li>Harus unik</li>
                                                        <li>Akan menjadi identifikasi utama jasa</li>
                                                    </ul>
                                                </li>
                                                <li><strong>Judul Kontrak</strong>:
                                                    <ul>
                                                        <li>Wajib diisi</li>
                                                        <li>Deskripsi lengkap dari jasa</li>
                                                    </ul>
                                                </li>
                                                <li><strong>Disiplin</strong>:
                                                    <ul>
                                                        <li>Wajib diisi</li>
                                                        <li>Gunakan nama disiplin yang valid</li>
                                                    </ul>
                                                </li>

                                            </ul>
                                            <hr>
                                            <p class="mb-0"><strong>Catatan:</strong>
                                            <ul>
                                                <li>Pilih EPS terlebih dahulu sebelum upload file</li>
                                                <li>Pastikan semua kolom wajib terisi dengan benar</li>
                                                <li>Data yang tidak sesuai format akan ditolak</li>
                                                <li>Hubungi admin jika mengalami kendala</li>
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
