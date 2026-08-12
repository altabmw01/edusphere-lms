<?php

namespace App\Http\Requests\Checkout;

use Illuminate\Foundation\Http\FormRequest;

class PlaceOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'billing_name' => ['required', 'string', 'max:255'],
            'billing_email' => ['required', 'email', 'max:255'],
            'billing_phone' => ['required', 'string', 'max:30'],
            'country' => ['required', 'string', 'max:100'],
            'division' => ['nullable', 'string', 'max:100'],
            'district' => ['nullable', 'string', 'max:100'],
            'thana' => ['nullable', 'string', 'max:100'],
            'address' => ['required', 'string', 'max:255'],
            'zip' => ['nullable', 'string', 'max:20'],
            'order_notes' => ['nullable', 'string', 'max:1000'],
            'payment_method' => ['required', 'in:cod,sslcommerz'],
            'coupon_code' => ['nullable', 'string', 'max:50'],
            'batches' => ['nullable', 'array'],
            'batches.*' => ['nullable', 'integer', 'exists:batches,id'],
        ];
    }
}
