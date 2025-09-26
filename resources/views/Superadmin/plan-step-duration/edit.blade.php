@extends('template_admin.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Plan Step Duration /</span> Edit Plan Step Duration
        </h4>

        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">Form Edit Plan Step Duration</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('superadmin.plan-step-duration.update', $planStepDuration->id_plan_step_duration) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="id_project">Project</label>
                                <div class="col-sm-10">
                                    <select class="form-select @error('id_project') is-invalid @enderror" id="id_project" name="id_project">
                                        <option value="">Pilih Project</option>
                                        @foreach($projects as $project)
                                            <option value="{{ $project->id_project }}" {{ old('id_project', $planStepDuration->id_project) == $project->id_project ? 'selected' : '' }}>
                                                {{ $project->tagno }} - {{ $project->remark }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_project')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            @php
                                $steps = [
                                    'notif' => 'Notif',
                                    'rekomend' => 'Rekomend',
                                    'job_plan' => 'Job Plan',
                                    'wo' => 'WO',
                                    'mat_reser' => 'Mat Reser',
                                    'pr' => 'PR',
                                    'tender' => 'Tender',
                                    'po' => 'PO',
                                    'gr' => 'GR',
                                    'gi' => 'GI',
                                    'eksekusi' => 'Eksekusi',
                                    'test_perfo' => 'Test Perfo',
                                    'sa' => 'SA',
                                    'update_or' => 'Update OR',
                                    'closed' => 'Closed'
                                ];
                            @endphp

                            @foreach($steps as $field => $label)
                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label" for="{{ $field }}">{{ $label }}</label>
                                    <div class="col-sm-10">
                                        <input type="number" class="form-control @error($field) is-invalid @enderror"
                                            id="{{ $field }}" name="{{ $field }}" value="{{ old($field, $planStepDuration->$field) }}"
                                            placeholder="Masukkan durasi {{ $label }}" />
                                        @error($field)
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            @endforeach

                            <div class="row justify-content-end">
                                <div class="col-sm-10">
                                    <button type="submit" class="btn btn-primary">Update</button>
                                    <a href="{{ route('superadmin.plan-step-duration') }}" class="btn btn-link">Kembali</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 