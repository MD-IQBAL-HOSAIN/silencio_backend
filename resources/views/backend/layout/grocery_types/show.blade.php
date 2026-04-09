@extends('backend.master')

@section('title', 'Grocery Type Details')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Grocery Types</a></li>
                    <li class="breadcrumb-item active">Details</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card box-shadow-0">
            <div class="card-body">
                <h1 class="text-center">Grocery Type Details</h1>
                <table class="table table-bordered">
                    <tbody>
                        <tr><th>Name</th><td>{{ $data->name ?? 'N/A' }}</td></tr>
                        <tr><th>Status</th><td>{{ ucfirst($data->status ?? 'N/A') }}</td></tr>
                    </tbody>
                </table>

                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('grocery-types.edit', $data->id) }}" class="btn btn-primary">Edit</a>
                    <a href="{{ route('grocery-types.index') }}" class="btn btn-primary">Back</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
