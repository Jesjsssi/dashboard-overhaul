@extends('template_admin.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Jasa /</span> Edit Jasa
        </h4>

        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">Form Edit Jasa</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('superadmin.jasa.update', $jasa->id_jasa) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="id_eps">EPS <span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <select class="form-select @error('id_eps') is-invalid @enderror" id="id_eps"
                                        name="id_eps" required>
                                        <option value="">Pilih EPS</option>
                                        @foreach($eps_list as $eps)
                                            <option value="{{ $eps->id_eps }}"
                                                {{ (old('id_eps', $jasa->id_eps) == $eps->id_eps) ? 'selected' : '' }}
                                                {{ !old('id_eps') && !$jasa->id_eps && $defaultEps && $eps->id_eps == $defaultEps->id_eps ? 'selected' : '' }}>
                                                {{ $eps->jenis_project }} - {{ $eps->tahun }} -
                                                {{ $eps->remark }}{{ $eps->default ? ' (Default)' : '' }}
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

                            <!-- Kode Jasa -->
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="kode_jasa">Kode Jasa <span
                                        class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control @error('kode_jasa') is-invalid @enderror"
                                        id="kode_jasa" name="kode_jasa" value="{{ old('kode_jasa', $jasa->kode_jasa) }}"
                                        placeholder="Masukkan Kode Jasa" required />
                                    @error('kode_jasa')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Judul Kontrak -->
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="judul_kontrak">Judul Kontrak <span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control @error('judul_kontrak') is-invalid @enderror"
                                        id="judul_kontrak" name="judul_kontrak"
                                        value="{{ old('judul_kontrak', $jasa->judul_kontrak) }}"
                                        placeholder="Masukkan Judul Kontrak" required />
                                </div>
                                @error('judul_kontrak')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="id_disiplin">Disiplin</label>
                                <div class="col-sm-10">
                                    <select class="form-select @error('id_disiplin') is-invalid @enderror" id="id_disiplin"
                                        name="id_disiplin">
                                        <option value="">Pilih Disiplin</option>
                                        @foreach($disiplins as $disiplin)
                                            <option value="{{ $disiplin->id_disiplin }}" {{ (old('id_disiplin', $jasa->id_disiplin) == $disiplin->id_disiplin) ? 'selected' : '' }}>
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

                            <!-- Planner -->
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="planner">Planner</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control @error('planner') is-invalid @enderror"
                                        id="planner" name="planner" value="{{ old('planner', $jasa->planner) }}"
                                        placeholder="Masukkan Planner" />
                                </div>
                                @error('planner')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- WO -->
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="wo">WO</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control @error('wo') is-invalid @enderror" id="wo"
                                        name="wo" value="{{ old('wo', $jasa->wo) }}" placeholder="Masukkan WO" />
                                </div>
                                @error('wo')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- PR -->
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="pr">PR</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control @error('pr') is-invalid @enderror" id="pr"
                                        name="pr" value="{{ old('pr', $jasa->pr) }}" placeholder="Masukkan PR" />
                                </div>
                                @error('pr')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- PO -->
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="po">PO</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control @error('po') is-invalid @enderror" id="po"
                                        name="po" value="{{ old('po', $jasa->po) }}" placeholder="Masukkan PO" />
                                </div>
                                @error('po')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Pemenang -->
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="pemenang">Pemenang</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control @error('pemenang') is-invalid @enderror"
                                        id="pemenang" name="pemenang" value="{{ old('pemenang', $jasa->pemenang) }}"
                                        placeholder="Masukkan Pemenang" />
                                </div>
                                @error('pemenang')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Keterangan -->
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="keterangan">Keterangan</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control @error('keterangan') is-invalid @enderror"
                                        id="keterangan" name="keterangan" value="{{ old('keterangan', $jasa->keterangan) }}"
                                        placeholder="Masukkan Keterangan" />
                                </div>
                                @error('keterangan')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="row justify-content-end">
                                <div class="col-sm-10">
                                    <button type="submit" class="btn btn-primary">Update</button>
                                    <a href="{{ route('superadmin.jasa') }}" class="btn btn-link">Kembali</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection