@extends('backend.master')

@section('title', 'Dynamic Page Details')

@section('title', 'Dynamic Page Details')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Dynamic Page</a></li>
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
                    <h1 class="text-center">Details Page</h1>
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th>Page Title</th>
                                <td>
                                    {{ $data->page_title ?? 'N/A' }}
                                </td>
                            </tr>
                            <tr>
                                <th>Page Content</th>
                                <td>
                                    {!! $data->page_content ?? 'N/A' !!}
                                </td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>{{ ucfirst($data->status ?? 'N/A') }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="d-flex justify-content-end">
                        <a href="{{ route('dynamic.index') }}" class="btn btn-primary">Back</a>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
