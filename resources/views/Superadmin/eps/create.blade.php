@extends('template_admin.layout')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">EPS /</span> Tambah EPS
    </h4>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Form Tambah EPS</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('superadmin.eps.store') }}" method="POST">
                        @csrf
                        <!-- Jenis Project -->
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="jenis_project">Project</label>
                            <div class="col-sm-10">
                                <select class="form-select @error('jenis_project') is-invalid @enderror"
                                    id="jenis_project"
                                    name="jenis_project"
                                    required>
                                    <option value="">Pilih Jenis Project</option>
                                    @foreach(App\Models\EPS::getJenisProjectList() as $value => $label)
                                        <option value="{{ $value }}" {{ old('jenis_project') == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('jenis_project')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="remark">Keterangan</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control @error('remark') is-invalid @enderror"
                                    id="remark" name="remark" value="{{ old('remark') }}"
                                    placeholder="Masukkan remark" />
                                @error('remark')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <!-- Tanggal Eksekusi -->
                        <div class="row mb-3" id="execution_date_container">
                            <label class="col-sm-2 col-form-label" for="execution_date">Tanggal Eksekusi</label>
                            <div class="col-sm-10">
                                <input type="date" class="form-control @error('execution_date') is-invalid @enderror"
                                    id="execution_date" name="execution_date" value="{{ old('execution_date') }}" />
                                @error('execution_date')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <!-- Tahun -->
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="tahun">Tahun</label>
                            <div class="col-sm-10">
                                <input type="number" class="form-control @error('tahun') is-invalid @enderror"
                                    id="tahun" name="tahun" value="{{ old('tahun') }}"
                                    placeholder="Masukkan tahun" min="2000" max="2099" required />
                                @error('tahun')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <!-- Cut Off Date -->
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="cutoff_date">Cut Off Date</label>
                            <div class="col-sm-10">
                                <input type="date" class="form-control @error('cutoff_date') is-invalid @enderror"
                                    id="cutoff_date" name="cutoff_date" value="{{ old('cutoff_date') }}"
                                    placeholder="Masukkan cutoff date" required />
                                @error('cutoff_date')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="row justify-content-end">
                            <div class="col-sm-10">
                                <button type="submit" class="btn btn-primary">Simpan</button>
                                <a href="{{ route('superadmin.eps') }}" class="btn btn-link">Kembali</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const jenisProjectSelect = document.getElementById('jenis_project');
    const executionDateContainer = document.getElementById('execution_date_container');
    const executionDateInput = document.getElementById('execution_date');
    const tahunInput = document.getElementById('tahun');

    function toggleExecutionDate() {
        const selectedValue = jenisProjectSelect.value;
        const tahun = tahunInput.value;

        if (selectedValue === 'Overhaul') {
            // Untuk Overhaul, set tanggal ke 1 Januari tahun yang diinput secara otomatis
            executionDateContainer.style.display = 'none';
            executionDateInput.removeAttribute('required');
            executionDateInput.readOnly = true;
            if (tahun) {
                executionDateInput.value = `${tahun}-01-01`;
            }
        } else if (selectedValue === 'Routine') {
            // Untuk Routine, kosongkan tanggal
            executionDateContainer.style.display = 'none';
            executionDateInput.removeAttribute('required');
            executionDateInput.value = '';
        } else {
            // Untuk jenis project lainnya, tampilkan field tanggal
            executionDateContainer.style.display = '';
            executionDateInput.setAttribute('required', 'required');
            executionDateInput.readOnly = false;
        }
    }

    // Jalankan saat halaman dimuat
    toggleExecutionDate();

    // Jalankan saat jenis project berubah
    jenisProjectSelect.addEventListener('change', function() {
        toggleExecutionDate();

        // Fokus ke field remark jika Overhaul atau Routine dipilih
        if (this.value === 'Overhaul' || this.value === 'Routine') {
            document.getElementById('remark').focus();
        }
    });

    // Jalankan saat tahun berubah (untuk mengupdate tanggal Overhaul)
    tahunInput.addEventListener('change', function() {
        if (jenisProjectSelect.value === 'Overhaul') {
            executionDateInput.value = `${this.value}-01-01`;
        }
    });

    // Tambahkan placeholder yang sesuai untuk remark berdasarkan jenis project
    jenisProjectSelect.addEventListener('change', function() {
        const remarkInput = document.getElementById('remark');
        if (this.value === 'Overhaul') {
            remarkInput.placeholder = 'Masukkan keterangan Overhaul';
        } else if (this.value === 'Routine') {
            remarkInput.placeholder = 'Masukkan keterangan Routine';
        } else {
            remarkInput.placeholder = 'Masukkan keterangan';
        }
    });
});
</script>
@endpush

@endsection
