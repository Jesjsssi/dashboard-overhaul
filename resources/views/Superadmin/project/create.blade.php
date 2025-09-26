@extends('template_admin.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Project /</span> Tambah Project
        </h4>

        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">Form Tambah Project</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('superadmin.project.store') }}" method="POST">
                            @csrf
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="id_eps">EPS</label>
                                <div class="col-sm-10">
                                    <select class="form-select @error('id_eps') is-invalid @enderror" id="id_eps" name="id_eps" required>
                                        <option value="">Pilih EPS</option>
                                        @foreach($eps_list as $eps)
                                            <option value="{{ $eps->id_eps }}"
                                                {{ old('id_eps') == $eps->id_eps ? 'selected' : '' }}
                                                {{ !old('id_eps') && $defaultEps && $eps->id_eps == $defaultEps->id_eps ? 'selected' : '' }}>
                                                {{ $eps->jenis_project }} - {{ $eps->tahun }} - {{ $eps->remark }}{{ $eps->default ? ' (Default)' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_eps')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="id_disiplin">Disiplin</label>
                                <div class="col-sm-10">
                                    <select class="form-select @error('id_disiplin') is-invalid @enderror" id="id_disiplin" name="id_disiplin" required>
                                        <option value="">Pilih Disiplin</option>
                                        @foreach($disiplin_list as $disiplin)
                                            <option value="{{ $disiplin->id_disiplin }}" {{ old('id_disiplin') == $disiplin->id_disiplin ? 'selected' : '' }}>
                                                {{ $disiplin->remark }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_disiplin')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <!-- Sub Disiplin -->
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="id_sub_disiplin">Sub Disiplin</label>
                                <div class="col-sm-10">
                                    <select class="form-select @error('id_sub_disiplin') is-invalid @enderror" id="id_sub_disiplin" name="id_sub_disiplin" required>
                                        <option value="">Pilih Sub Disiplin</option>
                                    </select>
                                    @error('id_sub_disiplin')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="kode_rkap">Kode RKAP <span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control @error('kode_rkap') is-invalid @enderror"
                                        id="kode_rkap" name="kode_rkap" value="{{ old('kode_rkap') }}"
                                        placeholder="Masukkan Kode RKAP" required />
                                    @error('kode_rkap')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="tagno">Tag No</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control @error('tagno') is-invalid @enderror"
                                        id="tagno" name="tagno" value="{{ old('tagno') }}"
                                        placeholder="Masukkan Tag No" />
                                    @error('tagno')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="remark">Program</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control @error('remark') is-invalid @enderror"
                                        id="remark" name="remark" value="{{ old('remark') }}"
                                        placeholder="Masukkan Remark" />
                                    @error('remark')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="weight_factor">Weight Factor</label>
                                <div class="col-sm-10">
                                    <input type="number" class="form-control @error('weight_factor') is-invalid @enderror"
                                        id="weight_factor" name="weight_factor" value="{{ old('weight_factor', 1) }}"
                                        step="0.01" min="0" required />
                                    <small class="text-muted">Nilai default adalah 1, namun dapat diubah sesuai kebutuhan</small>
                                    @error('weight_factor')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Kategori Material dan Jasa -->
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="kategori">Kategori <span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <select class="form-select @error('kategori') is-invalid @enderror" id="kategori" name="kategori" required>
                                        <option value="">Pilih Kategori</option>
                                        @foreach($kategori_list as $kategori)
                                            <option value="{{ $kategori }}" {{ old('kategori') == $kategori ? 'selected' : '' }}>
                                                {{ $kategori }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('kategori')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Step Date Fields -->
                            <h5 class="mb-3 mt-4">Step Dates</h5>
                            <div class="row">
                                @for ($i = 1; $i <= 15; $i++)
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="step_{{$i}}_date">Step {{$i}} Date</label>
                                        <input type="date" 
                                               class="form-control @error('step_'.$i.'_date') is-invalid @enderror" 
                                               id="step_{{$i}}_date" 
                                               name="step_{{$i}}_date" 
                                               value="{{ old('step_'.$i.'_date') }}" />
                                        @error('step_'.$i.'_date')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                @endfor
                            </div>

                            

                            <div class="row justify-content-end mt-3">
                                <div class="col-sm-10">
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                    <a href="{{ route('superadmin.project') }}" class="btn btn-link">Batal</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const disiplinSelect = document.getElementById('id_disiplin');
        const subDisiplinSelect = document.getElementById('id_sub_disiplin');

        // Fungsi untuk memuat sub disiplin
        function loadSubDisiplin(idDisiplin) {
            if (!idDisiplin) {
                subDisiplinSelect.innerHTML = '<option value="">Pilih Sub Disiplin</option>';
                return;
            }

            fetch(`{{ route('superadmin.master-sub-disiplin.get-by-disiplin', ['id_disiplin' => ':id_disiplin']) }}`.replace(':id_disiplin', idDisiplin))
                .then(response => response.json())
                .then(data => {
                    let options = '<option value="">Pilih Sub Disiplin</option>';
                    data.forEach(item => {
                        const selected = item.id_sub_disiplin == "{{ old('id_sub_disiplin') }}" ? 'selected' : '';
                        options += `<option value="${item.id_sub_disiplin}" ${selected}>${item.remark}</option>`;
                    });
                    subDisiplinSelect.innerHTML = options;
                })
                .catch(error => {
                    console.error('Error:', error);
                    subDisiplinSelect.innerHTML = '<option value="">Error loading sub disiplin</option>';
                });
        }

        // Event listener untuk perubahan pada select disiplin
        disiplinSelect.addEventListener('change', function() {
            loadSubDisiplin(this.value);
        });

        // Load sub disiplin saat halaman dimuat jika disiplin sudah dipilih
        if (disiplinSelect.value) {
            loadSubDisiplin(disiplinSelect.value);
        }
    });
</script>
@endsection