<?php

namespace App\Http\Requests\Admin;


use App\Http\Requests\FormRequest;

class AuthorizationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'username' => 'required',
            'password' => 'required|alpha_dash|min:6'
        ];
    }
}
