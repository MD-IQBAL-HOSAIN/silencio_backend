@extends('backend.master')

@section('title', 'Edit Grocery Type')

@section('content')
<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h1 class="page-title">Edit Grocery Type</h1>
    </div>
    <div class="ms-auto d-flex align-items-center gap-2">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Grocery Type</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 col-xl-12 col-md-12 col-sm-12">
        <div class="card box-shadow-0">
            <div class="card-body">
                <form action="{{ route('grocery-types.update', $data->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $data->name) }}" placeholder="e.g. Fruit">
                        @error('name')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="{{ route('grocery-types.index') }}" class="btn btn-primary">Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
