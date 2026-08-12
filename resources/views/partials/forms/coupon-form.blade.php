@php($coupon = $coupon ?? null)

<div class="filter-card" style="max-width:640px;">
    <div class="row g-3">
        <div class="col-md-6"><x-form.input name="code" label="Coupon Code" :value="$coupon?->code" required hint="Letters, numbers, dashes only" /></div>
        <div class="col-md-6"><x-form.select name="type" label="Discount Type" :options="['percentage' => 'Percentage', 'fixed' => 'Fixed Amount']" :value="$coupon?->type" required /></div>
        <div class="col-md-6"><x-form.input name="value" type="number" step="0.01" label="Discount Value" :value="$coupon?->value" required /></div>
        <div class="col-md-6"><x-form.input name="maximum_discount" type="number" step="0.01" label="Maximum Discount ({{ config('lms.currency_symbol') }})" :value="$coupon?->maximum_discount" /></div>
        <div class="col-md-6"><x-form.input name="minimum_purchase" type="number" step="0.01" label="Minimum Purchase ({{ config('lms.currency_symbol') }})" :value="$coupon?->minimum_purchase" /></div>
        <div class="col-md-6"><x-form.select name="applicable_to" label="Applicable To" :options="['all' => 'All Products', 'courses' => 'Courses Only', 'books' => 'Books Only']" :value="$coupon?->applicable_to" required /></div>
        <div class="col-md-6"><x-form.input name="usage_limit" type="number" label="Total Usage Limit" :value="$coupon?->usage_limit" hint="Leave blank for unlimited" /></div>
        <div class="col-md-6"><x-form.input name="per_user_limit" type="number" label="Per-User Limit" :value="$coupon?->per_user_limit ?? 1" required /></div>
        <div class="col-md-6"><x-form.input name="expires_at" type="date" label="Expiry Date" :value="$coupon?->expires_at?->format('Y-m-d')" /></div>
        <div class="col-md-6 d-flex align-items-end">
            <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" name="status" value="1" id="status" @checked($coupon?->status ?? true)>
                <label class="form-check-label small" for="status">Active</label>
            </div>
        </div>
    </div>
    <button class="btn btn-brand w-100 mt-2">{{ $coupon ? 'Update Coupon' : 'Create Coupon' }}</button>
</div>
