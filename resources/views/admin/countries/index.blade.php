@extends('layouts.app')

@section('title', 'Countries')
@section('page-title', 'Countries & Shipping')

@section('content')
<div class="alert alert-info small"><i class="bi bi-info-circle me-1"></i> Bangladesh's shipping cost is set separately under <a href="{{ route('admin.settings.edit') }}">Settings → Book Shipping</a> (it depends on district, not a flat country rate). The rate below only applies to every other country.</div>

<div class="d-flex justify-content-end mb-4">
    <button class="btn btn-brand" data-bs-toggle="modal" data-bs-target="#createCountryModal"><i class="bi bi-plus"></i> New Country</button>
</div>

<div class="table-brand">
    <table class="table mb-0">
        <thead><tr><th>Code</th><th>Country</th><th>Shipping Cost</th><th></th></tr></thead>
        <tbody>
            @forelse($countries as $country)
                <tr>
                    <td><code>{{ $country->country_code }}</code></td>
                    <td>{{ $country->country_name }}</td>
                    <td>{{ money($country->shipping_cost) }}</td>
                    <td class="text-end">
                        <button class="btn btn-icon-circle" data-bs-toggle="modal" data-bs-target="#editCountryModal{{ $country->id }}"><i class="bi bi-pencil"></i></button>
                        <form action="{{ route('admin.countries.destroy', $country) }}" method="POST" class="d-inline" data-confirm="Delete this country?">
                            @csrf @method('DELETE')
                            <button class="btn btn-icon-circle text-danger"><i class="bi bi-trash3"></i></button>
                        </form>
                    </td>
                </tr>

                <div class="modal fade" id="editCountryModal{{ $country->id }}" tabindex="-1">
                    <div class="modal-dialog"><div class="modal-content" style="border-radius:var(--radius-lg);">
                        <div class="modal-body p-4">
                            <h5 class="mb-3">Edit Country</h5>
                            <form method="POST" action="{{ route('admin.countries.update', $country) }}">
                                @csrf @method('PUT')
                                <x-form.input name="country_code" label="ISO Code" :value="$country->country_code" hint="e.g. US, IN, GB" required />
                                <x-form.input name="country_name" label="Country Name" :value="$country->country_name" required />
                                <x-form.input name="shipping_cost" type="number" step="0.01" label="Shipping Cost ({{ config('lms.currency_symbol') }})" :value="$country->shipping_cost" required />
                                <button class="btn btn-brand w-100">Save Changes</button>
                            </form>
                        </div>
                    </div></div>
                </div>
            @empty
                <tr><td colspan="4" class="text-center text-muted py-5">No countries added yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $countries->links() }}</div>

<div class="modal fade" id="createCountryModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content" style="border-radius:var(--radius-lg);">
        <div class="modal-body p-4">
            <h5 class="mb-3">New Country</h5>
            <form method="POST" action="{{ route('admin.countries.store') }}">
                @csrf
                <x-form.input name="country_code" label="ISO Code" hint="e.g. US, IN, GB" required />
                <x-form.input name="country_name" label="Country Name" required />
                <x-form.input name="shipping_cost" type="number" step="0.01" label="Shipping Cost ({{ config('lms.currency_symbol') }})" value="0" required />
                <button class="btn btn-brand w-100">Add Country</button>
            </form>
        </div>
    </div></div>
</div>
@endsection
