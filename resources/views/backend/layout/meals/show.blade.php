@extends('backend.master')

@section('title', 'Meal Details')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Meals</a></li>
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
                <h1 class="text-center">Meal Details</h1>
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th>Image</th>
                            <td>
                                @if(!empty($data->image))
                                    <img src="{{ asset($data->image) }}" alt="Meal" style="width:120px;height:120px;object-fit:cover;border-radius:8px;">
                                @else
                                    N/A
                                @endif
                            </td>
                        </tr>
                        <tr><th>Title</th><td>{{ $data->title ?? 'N/A' }}</td></tr>
                        <tr><th>Meal Type</th><td>{{ ucfirst($data->meal_type ?? 'N/A') }}</td></tr>
                        <tr><th>Description</th><td>{{ $data->description ?? 'N/A' }}</td></tr>
                        <tr><th>Time</th><td>{{ $data->time ?? 'N/A' }}</td></tr>
                        <tr><th>Servings</th><td>{{ $data->servings ?? 'N/A' }}</td></tr>
                        <tr><th>Calories</th><td>{{ $data->calories ?? 'N/A' }}</td></tr>
                        <tr><th>Protein</th><td>{{ $data->protein ?? 'N/A' }}</td></tr>
                        <tr><th>Carbs</th><td>{{ $data->carbs ?? 'N/A' }}</td></tr>
                        <tr><th>Fats</th><td>{{ $data->fats ?? 'N/A' }}</td></tr>
                        <tr><th>Status</th><td>{{ ucfirst($data->status ?? 'N/A') }}</td></tr>
                        <tr>
                            <th>Ingredients & <br/>
                             Instructions
                             </th>
                            <td>
                                @php
                                    $ingredientsByType = $data->ingredients->groupBy(function ($ingredient) {
                                        return $ingredient->groceryType->name ?? 'Others';
                                    });
                                @endphp

                                <table class="table table-bordered mb-0">
                                    <thead>
                                        <tr>
                                            <th width="55%">Ingredients</th>
                                            <th width="45%">Instructions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="align-top">
                                                @forelse($ingredientsByType as $type => $ingredients)
                                                    <h6 class="mb-2 mt-2">{{ $type }}</h6>
                                                    @foreach($ingredients as $ingredient)
                                                        <div class="d-flex justify-content-between align-items-center border rounded px-2 py-1 mb-1">
                                                            <span>• {{ $ingredient->title }}</span>
                                                            <span>{{ $ingredient->quantity ?? 'N/A' }}</span>
                                                        </div>
                                                    @endforeach
                                                @empty
                                                    <div>N/A</div>
                                                @endforelse
                                            </td>
                                            <td class="align-top">
                                                <ol class="mb-0 ps-3">
                                                    @forelse($data->instructions as $instruction)
                                                        <li class="mb-1">{{ $instruction->title }}</li>
                                                    @empty
                                                        <li>N/A</li>
                                                    @endforelse
                                                </ol>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('meals.edit', $data->id) }}" class="btn btn-primary">Edit</a>
                    <a href="{{ route('meals.index') }}" class="btn btn-primary">Back</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
