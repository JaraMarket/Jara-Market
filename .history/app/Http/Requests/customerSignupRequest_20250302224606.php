<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class customerSignupRequest extends FormRequest
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
        return [
            "firstname" => "required|string|max:30",
            "lastname" => "required|string|max:30",
            "email" => "required|email|max:255|unique:customers,email",
            "password" => "required|min:8",
        ];
    }

    public function messages()
    {
        return [
            'firstname.required' => 'Please enter your name.',
            'name.string' => 'The name must be a valid text.',
            'name.required' => 'Please enter your name.',
            'name.string' => 'The name must be a valid text.',
            'name.max' => 'The name cannot exceed 255 characters.',
            'email.required' => 'An email address is required to register.',
            'email.email' => 'Please provide a valid email address.',
            'email.max' => 'The email address cannot exceed 255 characters.',
            'email.unique' => 'This email is already registered. Please use a different one.',
            'password.required' => 'A password is required.',
            'password.min' => 'Your password must be at least 8 characters long.',
        ];
    }
}
