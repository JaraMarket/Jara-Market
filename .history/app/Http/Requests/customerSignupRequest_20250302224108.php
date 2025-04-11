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
            'firstname' => 'required|uuid',
            'lastname' => 'required|string',
            'email' => 'required|string',
            'password' => 'required|string',
            'year' => 'required|string',
            'colour' => 'required|string',
            'doc_type' => 'required|string',
            'document' => 'required|array|min:1|max:5', // Multiple images allowed, between 1 and 5
            'document.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // Validate each document as an image
        ];
    }

    public function messages()
    {
        return [
            'rider_id.required' => 'Please provide the rider ID.',
            'make.required' => 'The bike make is required. Please enter the manufacturer.',
            'model.required' => 'The bike model is required. Please specify the model.',
            'plate_number.required' => 'A plate number is required for the bike.',
            'year.required' => 'Please provide the year of manufacture for the bike.',
            'colour.required' => 'Please specify the color of the bike.',
            'doc_type.required' => 'Please indicate the type of document you are submitting.',
            'document.required' => 'At least one document image is required. Please upload up to 5 document images.',
            'document.array' => 'Documents must be an array.',
            'document.min' => 'You must upload at least 1 document image.',
            'document.max' => 'You cannot upload more than 5 document images.',
            'document.*.image' => 'Each document must be a valid image file.',
            'document.*.mimes' => 'Each document image must be a JPEG, PNG, JPG, or GIF file.',
            'document.*.max' => 'Each document image size must not exceed 2MB.',
            'vehicle_photo.required' => 'At least one vehicle photo is required. Please upload up to 5 photos.',
            'vehicle_photo.array' => 'Vehicle photos must be an array.',
            'vehicle_photo.min' => 'You must upload at least 1 vehicle photo.',
            'vehicle_photo.max' => 'You cannot upload more than 5 vehicle photos.',
            'vehicle_photo.*.image' => 'Each vehicle photo must be a valid image file.',
            'vehicle_photo.*.mimes' => 'Each vehicle photo must be a JPEG, PNG, JPG, or GIF image.',
            'vehicle_photo.*.max' => 'Each vehicle photo size must not exceed 2MB.',
        ];
    }
}
