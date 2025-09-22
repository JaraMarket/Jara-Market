<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;


class ProductRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'preparation_steps' => ['nullable', 'string'],
            'price' => ['required', 'min:0'],
            'discount_price' => ['nullable', 'min:0'],
            'rating' => ['nullable'],
            'stock' => ['nullable'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['exists:categories,id'],
            'ingredients' => ['nullable', 'array'],
            'ingredients.*.ingredient_id' => ['nullable', 'exists:ingredients,id'],
            'ingredients.*.quantity' => ['nullable'],
            'ingredients.*.unit' => ['nullable', 'string'],
            'image_url' => ['nullable', 'mimes:jpg,jpeg,png', 'max:2048']
        ];
    }
}
