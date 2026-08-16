<?php

namespace App\Http\Requests\Checkout;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Courses are digital — no shipping address needed at all, just billing
 * contact info. Quantity is always 1 and never exposed on this form.
 */
class PlaceCourseOrderRequest extends FormRequest
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
            'order_notes' => ['nullable', 'string', 'max:1000'],
            'payment_method' => ['required', 'in:cod,sslcommerz'],
            'coupon_code' => ['nullable', 'string', 'max:50'],
            'batches' => ['nullable', 'array'],
            'batches.*' => ['nullable', 'integer', 'exists:batches,id'],
        ];
    }
}
