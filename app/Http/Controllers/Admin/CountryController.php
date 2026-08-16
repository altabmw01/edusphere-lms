<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CountryController extends Controller
{
    public function index(): View
    {
        return view('admin.countries.index', [
            'countries' => Country::orderBy('country_name')->paginate(30),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'country_code' => ['required', 'string', 'max:5', 'unique:countries,country_code'],
            'country_name' => ['required', 'string', 'max:100'],
            'shipping_cost' => ['required', 'numeric', 'min:0'],
        ]);

        Country::create($data);

        return back()->with('status', 'Country added.');
    }

    public function update(Request $request, Country $country): RedirectResponse
    {
        $data = $request->validate([
            'country_code' => ['required', 'string', 'max:5', 'unique:countries,country_code,' . $country->id],
            'country_name' => ['required', 'string', 'max:100'],
            'shipping_cost' => ['required', 'numeric', 'min:0'],
        ]);

        $country->update($data);

        return back()->with('status', 'Country updated.');
    }

    public function destroy(Country $country): RedirectResponse
    {
        $country->delete();

        return back()->with('status', 'Country deleted.');
    }
}
