<?php

namespace App\Http\Requests\Admin;


use App\Http\Requests\FormRequest;
use Illuminate\Validation\Rule;

class AdminUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required',
            'username' => [
                'required',
                Rule::unique('users')->ignore($this->route('user')),
            ],
            'password' => 'required||min:6'
        ];
    }
}
