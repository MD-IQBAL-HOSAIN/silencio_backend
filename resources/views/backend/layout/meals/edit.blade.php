@extends('backend.master')

@section('title', 'Edit Meal')

@section('content')
    <div class="page-header d-flex align-items-center justify-content-between">
        <div>
            <h1 class="page-title">Edit Meal</h1>
        </div>
        <div class="ms-auto d-flex align-items-center gap-2">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Meal</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 col-xl-12 col-md-12 col-sm-12">
            <div class="card box-shadow-0">
                <div class="card-body">
                    <form action="{{ route('meals.update', $data->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" name="title" id="title" class="form-control"
                                value="{{ old('title', $data->title) }}" placeholder="Meal title">
                            @error('title')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" id="description" class="form-control" rows="3" placeholder="Description">{{ old('description', $data->description) }}</textarea>
                            @error('description')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="meal_type" class="form-label">Meal Type</label>
                                <select name="meal_type" id="meal_type" class="form-control">
                                    <option value="">Select Type</option>
                                    <option value="lunch"
                                        {{ old('meal_type', $data->meal_type) == 'lunch' ? 'selected' : '' }}>Lunch</option>
                                    <option value="dinner"
                                        {{ old('meal_type', $data->meal_type) == 'dinner' ? 'selected' : '' }}>Dinner
                                    </option>
                                    <option value="breakfast"
                                        {{ old('meal_type', $data->meal_type) == 'breakfast' ? 'selected' : '' }}>Breakfast
                                    </option>
                                </select>
                                @error('meal_type')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="time" class="form-label">Time</label>
                                <input type="text" name="time" id="time" class="form-control"
                                    value="{{ old('time', $data->time) }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="servings" class="form-label">Servings</label>
                                <input type="text" name="servings" id="servings" class="form-control"
                                    value="{{ old('servings', $data->servings) }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="calories" class="form-label">Calories</label>
                                <input type="text" name="calories" id="calories" class="form-control"
                                    value="{{ old('calories', $data->calories) }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="protein" class="form-label">Protein</label>
                                <input type="text" name="protein" id="protein" class="form-control"
                                    value="{{ old('protein', $data->protein) }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="carbs" class="form-label">Carbs</label>
                                <input type="text" name="carbs" id="carbs" class="form-control"
                                    value="{{ old('carbs', $data->carbs) }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="fats" class="form-label">Fats</label>
                                <input type="text" name="fats" id="fats" class="form-control"
                                    value="{{ old('fats', $data->fats) }}">
                            </div>
                        </div>
                        {{-- Image Upload Update --}}
                        <div class="mb-3">
                            <label for="image" class="form-label">Image</label>
                            <input class="form-control dropify" type="file" name="image" id="image"
                                accept="image/*"
                                @isset($data->image)
                                                data-default-file="{{ asset($data->image) }}"
                                    @endisset>
                            @error('image')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Ingredients</label>
                            @php
                                $oldIngredients = old(
                                    'ingredients',
                                    $data->ingredients
                                        ->map(
                                            fn($ingredient) => [
                                                'grocery_type_id' => $ingredient->grocery_type_id,
                                                'title' => $ingredient->title,
                                                'quantity' => $ingredient->quantity,
                                            ],
                                        )
                                        ->toArray(),
                                );
                                if (empty($oldIngredients)) {
                                    $oldIngredients = [['grocery_type_id' => '', 'title' => '', 'quantity' => '']];
                                }
                                $groceryTypeOptions = $groceryTypes
                                    ->map(
                                        fn($type) => '<option value="' .
                                            $type->id .
                                            '">' .
                                            e($type->name) .
                                            '</option>',
                                    )
                                    ->implode('');
                            @endphp
                            <div id="ingredients-wrapper">
                                @foreach ($oldIngredients as $index => $ingredient)
                                    <div class="ingredient-item border rounded p-2 mb-2">
                                        <div class="row g-2 align-items-end">
                                            <div class="col-md-4">
                                                <label class="form-label">Grocery Type</label>
                                                <select name="ingredients[{{ $index }}][grocery_type_id]"
                                                    class="form-control">
                                                    <option value="">Select Type</option>
                                                    @foreach ($groceryTypes as $type)
                                                        <option value="{{ $type->id }}"
                                                            {{ (string) ($ingredient['grocery_type_id'] ?? '') === (string) $type->id ? 'selected' : '' }}>
                                                            {{ $type->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Ingredient</label>
                                                <input type="text" name="ingredients[{{ $index }}][title]"
                                                    class="form-control" value="{{ $ingredient['title'] ?? '' }}"
                                                    placeholder="Ingredient name">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Quantity</label>
                                                <input type="text" name="ingredients[{{ $index }}][quantity]"
                                                    class="form-control" value="{{ $ingredient['quantity'] ?? '' }}"
                                                    placeholder="e.g. 2 slices">
                                            </div>
                                            <div class="col-md-1 d-grid">
                                                <button type="button" class="btn btn-danger remove-ingredient">X</button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm" id="add-ingredient">+ Add
                                Ingredient</button>
                            @error('ingredients')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                            @error('ingredients.*.grocery_type_id')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                            @error('ingredients.*.title')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                            @error('ingredients.*.quantity')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Instructions</label>
                            @php
                                $oldInstructions = old('instructions', $data->instructions->pluck('title')->toArray());
                                if (empty($oldInstructions)) {
                                    $oldInstructions = [''];
                                }
                            @endphp
                            <div id="instructions-wrapper">
                                @foreach ($oldInstructions as $instruction)
                                    <div class="instruction-item d-flex gap-2 mb-2">
                                        <input type="text" name="instructions[]" class="form-control"
                                            value="{{ $instruction }}" placeholder="Instruction">
                                        <button type="button" class="btn btn-danger remove-instruction">X</button>
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm" id="add-instruction">+ Add
                                Instruction</button>
                            @error('instructions')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                            @error('instructions.*')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <button type="submit" class="btn btn-primary">Update</button>
                            <a href="{{ route('meals.index') }}" class="btn btn-primary">Back</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function ingredientRow(index) {
            const options = `{!! $groceryTypeOptions !!}`;
            return `<div class="ingredient-item border rounded p-2 mb-2">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">Grocery Type</label>
                            <select name="ingredients[${index}][grocery_type_id]" class="form-control">
                                <option value="">Select Type</option>
                                ${options}
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Ingredient</label>
                            <input type="text" name="ingredients[${index}][title]" class="form-control" placeholder="Ingredient name">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Quantity</label>
                            <input type="text" name="ingredients[${index}][quantity]" class="form-control" placeholder="e.g. 2 slices">
                        </div>
                        <div class="col-md-1 d-grid">
                            <button type="button" class="btn btn-danger remove-ingredient">X</button>
                        </div>
                    </div>
                </div>`;
        }

        function instructionRow(value = '') {
            return `<div class="instruction-item d-flex gap-2 mb-2">
                    <input type="text" name="instructions[]" class="form-control" value="${value}" placeholder="Instruction">
                    <button type="button" class="btn btn-danger remove-instruction">X</button>
                </div>`;
        }

        document.getElementById('add-ingredient').addEventListener('click', function() {
            const index = document.querySelectorAll('.ingredient-item').length;
            document.getElementById('ingredients-wrapper').insertAdjacentHTML('beforeend', ingredientRow(index));
        });

        document.getElementById('add-instruction').addEventListener('click', function() {
            document.getElementById('instructions-wrapper').insertAdjacentHTML('beforeend', instructionRow());
        });

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-ingredient')) {
                const items = document.querySelectorAll('.ingredient-item');
                if (items.length > 1) {
                    e.target.closest('.ingredient-item').remove();
                }
            }

            if (e.target.classList.contains('remove-instruction')) {
                const items = document.querySelectorAll('.instruction-item');
                if (items.length > 1) {
                    e.target.closest('.instruction-item').remove();
                }
            }
        });
    </script>
@endpush
