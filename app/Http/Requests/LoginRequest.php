<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Allow everyone to attempt login
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'username' => 'required|string|max:255',
            'password' => 'required|string',
        ];
    }

    /**
     * Customize error messages (optional)
     */
    public function messages(): array
    {
        return [
            'username.required' => __('Username is required'),
            'password.required' => __('Password is required')
        ];
    }
}
