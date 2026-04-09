@extends('backend.master')

@section('title', 'Dashboard | faq Create form')

@push('styles')
@endpush

@section('content')

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <h4 class="mb-sm-0">Create FAQ</h4>
                    {{-- <a href="{{ route('feature.faq.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="mdi mdi-arrow-left"></i> Back
                    </a> --}}
                </div>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">FAQ</a></li>
                        <li class="breadcrumb-item active">Create FAQ</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-lg-12 col-xl-12 col-md-12 col-sm-12">
            <div class="card box-shadow-0">
                <div class="card-body">
                    <form action="{{ route('faq.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        {{-- Question --}}
                        <div class="mb-3">
                            <label for="question" class="form-label">Question</label>
                            <input type="text" name="question" id="question" class="form-control"
                                value="{{ old('question') }}" placeholder="Enter question">
                            @error('question')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{--  Answer --}}
                        <div class="mb-3">
                            <label for="answer" class="form-label">Answer</label>
                            <textarea name="answer" id="answer" class="form-control ck-editor" rows="5" placeholder="Enter answer">{{ old('answer') }}</textarea>
                            @error('answer')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">Create</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- end row -->

@endsection
@push('scripts')
@endpush
