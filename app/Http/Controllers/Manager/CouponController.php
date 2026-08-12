<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CouponController extends Controller
{
    public function index(): View
    {
        return view('manager.coupons.index', ['coupons' => Coupon::latest()->paginate(20)]);
    }

    public function create(): View
    {
        return view('manager.coupons.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['created_by'] = $request->user()->id;

        Coupon::create($data);

        return redirect()->route('manager.coupons.index')->with('status', 'Coupon created.');
    }

    public function edit(Coupon $coupon): View
    {
        return view('manager.coupons.edit', ['coupon' => $coupon]);
    }

    public function update(Request $request, Coupon $coupon): RedirectResponse
    {
        $coupon->update($this->validated($request));

        return back()->with('status', 'Coupon updated.');
    }

    public function destroy(Coupon $coupon): RedirectResponse
    {
        $coupon->delete();

        return back()->with('status', 'Coupon deleted.');
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'code' => ['required', 'string', 'max:50', 'alpha_dash'],
            'type' => ['required', 'in:percentage,fixed'],
            'value' => ['required', 'numeric', 'min:0'],
            'minimum_purchase' => ['nullable', 'numeric', 'min:0'],
            'maximum_discount' => ['nullable', 'numeric', 'min:0'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'per_user_limit' => ['required', 'integer', 'min:1'],
            'applicable_to' => ['required', 'in:all,courses,books'],
            'expires_at' => ['nullable', 'date', 'after:today'],
            'status' => ['boolean'],
        ]);
    }
}
