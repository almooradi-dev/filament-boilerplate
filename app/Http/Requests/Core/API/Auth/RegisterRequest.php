<?php

namespace App\Http\Requests\Core\API\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required_without:username|email|unique:users,email',
            'username' => 'required_without:email|unique:users,username',
            'country_code' => 'nullable|integer',
            'phone' => [
                'nullable',
                'numeric',
                'min:7',
                Rule::unique("users")->where(fn ($query) => $query->where([
                    ["country_code", "=", $this->country_code],
                    ["phone", "=", $this->phone]
                ])) // TODO: indexing by "country_code" and "phone"
            ],
            'password' => 'required|min:8|confirmed',
            'terms_and_conditions' => 'accepted',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'country_code' => str_replace('+', '', $this->country_code),
            'phone' => ltrim($this->phone, '0'),
        ]);
    }
}