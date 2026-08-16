<?php

namespace App\Http\Requests\Checkout;

use App\Models\Country;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class PlaceBookOrderRequest extends FormRequest
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
            'quantity' => ['required', 'integer', 'min:1', 'max:20'],
            'country_id' => ['required', 'integer', 'exists:countries,id'],
            'division_id' => ['nullable', 'integer', 'exists:divisions,id'],
            'district_id' => ['nullable', 'integer', 'exists:districts,id'],
            'thana_id' => ['nullable', 'integer', 'exists:thanas,id'],
            'union_id' => ['nullable', 'integer', 'exists:unions,id'],
            'address' => ['required', 'string', 'max:255'],
            'zip' => ['nullable', 'string', 'max:20'],
            'order_notes' => ['nullable', 'string', 'max:1000'],
            'payment_method' => ['required', 'in:cod,sslcommerz'],
            'coupon_code' => ['nullable', 'string', 'max:50'],
            'batches' => ['nullable', 'array'],
            'batches.*' => ['nullable', 'integer', 'exists:batches,id'],
        ];
    }

    /**
     * Division/District/Thana are only required when the selected country is
     * Bangladesh — checked here rather than a declarative rule since it
     * depends on a DB lookup of the submitted country_id. Union stays
     * optional at every level (finest-grained, often skipped in practice).
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $countryId = $this->input('country_id');

            if (! $countryId) {
                return;
            }

            $country = Country::find($countryId);

            if ($country && $country->isBangladesh()) {
                foreach (['division_id' => 'division', 'district_id' => 'district', 'thana_id' => 'thana'] as $field => $label) {
                    if (! $this->filled($field)) {
                        $validator->errors()->add($field, "Please select your {$label}.");
                    }
                }
            }
        });
    }
}
