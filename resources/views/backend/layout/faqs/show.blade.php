@extends('backend.master')

@section('title', 'FAQ Details')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">FAQ</a></li>
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
                                <th>Status</th>
                                <td>{{ ucfirst($data->status ?? 'N/A') }}</td>
                            </tr>
                            <tr>
                                <th>Question</th>
                                <td>{!! $data->question ?? 'N/A' !!}</td>
                            </tr>
                            <tr>
                                <th>Answer</th>
                                <td>
                                    {!! $data->answer ?? 'N/A' !!}
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="d-flex justify-content-end">
                        <a href="{{ route('faq.index') }}" class="btn btn-primary">Back</a>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
