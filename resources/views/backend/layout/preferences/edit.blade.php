@extends('backend.master')

@section('title', 'Edit Preference')

@section('content')

    {{-- PAGE-HEADER --}}
    <div class="page-header d-flex align-items-center justify-content-between">
        <div>
            <h1 class="page-title">Edit Preference</h1>
        </div>
        <div class="ms-auto d-flex align-items-center gap-2">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Preference</li>
            </ol>
        </div>
    </div>
    {{-- PAGE-HEADER --}}
    <div class="row">
        <div class="col-lg-12 col-xl-12 col-md-12 col-sm-12">
            <div class="card box-shadow-0">
                <div class="card-body">
                    <form action="{{ route('preference.update', $data->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Preference Type Edit --}}
                        <div class="mb-3">
                            <label for="type" class="form-label">Preference Type</label>
                            <select name="type" id="type" class="form-control">
                                <option value="">Select Type</option>
                                <option value="dietary" {{ old('type', $data->type) == 'dietary' ? 'selected' : '' }}>
                                    Dietary</option>
                                <option value="cuisine" {{ old('type', $data->type) == 'cuisine' ? 'selected' : '' }}>
                                    Cuisine</option>
                                <option value="allergies" {{ old('type', $data->type) == 'allergies' ? 'selected' : '' }}>
                                    Allergies</option>
                            </select>
                            @error('type')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Preference Name Edit --}}
                        <div class="mb-3">
                            <label for="name" class="form-label">Preference Name</label>
                            <input type="text" name="name" id="name" class="form-control"
                                value="{{ old('name', $data->name) }}" placeholder="Preference Name">
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
@endpush
