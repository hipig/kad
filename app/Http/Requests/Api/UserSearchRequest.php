<?php

namespace App\Http\Requests\Api;


use App\Http\Requests\FormRequest;

class UserSearchRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'wallet_account' => 'required_without_all:username,name',
            'username' => 'required_without_all:wallet_account,name',
            'name' => 'required_without_all:wallet_account,username'
        ];
    }
}
