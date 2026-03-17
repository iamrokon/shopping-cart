<?php

namespace App\Http\Requests\Cart;

use Illuminate\Foundation\Http\FormRequest;

class BatchSyncCartRequest extends FormRequest
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
            'items'              => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity'   => ['required', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required'              => 'Cart items are required.',
            'items.array'                 => 'Items must be an array.',
            'items.*.product_id.required' => 'Each item must have a product_id.',
            'items.*.product_id.exists'   => 'One or more products not found.',
            'items.*.quantity.required'   => 'Each item must have a quantity.',
            'items.*.quantity.min'        => 'Quantity must be at least 0.',
        ];
    }
}
