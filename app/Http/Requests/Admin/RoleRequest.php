<?php

namespace App\Http\Requests\Admin;


use App\Http\Requests\FormRequest;
use Illuminate\Validation\Rule;

class RoleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'label' => 'required',
            'name' => [
                'required',
                Rule::unique('roles')->ignore($this->route('role')),
            ]
        ];
    }
}
