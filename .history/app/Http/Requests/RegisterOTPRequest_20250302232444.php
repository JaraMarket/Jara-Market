<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterOTPRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
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
            'otp.required' => 'OTP is required.',

        ];
    }
}
