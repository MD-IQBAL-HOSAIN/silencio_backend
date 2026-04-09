<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
         $rules = [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'string', 'min:4'],
                'avatar'   => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,svg,webp,ico,bmp,tiff,avif,jfif,heic', 'max:2048'],
            'role' => ['nullable']
        ];

        if ($this->routeIs('system-user.store')) {
            $rules['email'] = ['required', 'email', 'unique:users,email'];

        }
        // dd($rules);
        return $rules;
    }
}
