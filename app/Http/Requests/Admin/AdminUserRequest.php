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
        if ($this->isMethod('PUT')) {
            return [
                'name' => 'required',
                'username' => [
                    'required',
                    Rule::unique('admin_users')->ignore($this->route('user')),
                ],
            ];
        }

        return [
            'name' => 'required',
            'username' => [
                'required',
                Rule::unique('admin_users'),
            ],
            'password' => 'required|min:6'
        ];
    }
}
