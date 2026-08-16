<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(protected CartService $cartService)
    {
    }

    /**
     * This project sells one course/book at a time, so "Add to Cart" doubles as
     * "start a purchase". A guest is sent through the mobile-number identify flow
     * before anything is actually added to a cart; an authenticated user goes
     * straight to checkout.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'type' => ['required', 'in:course,book'],
            'id' => ['required', 'integer'],
        ]);

        $type = $request->string('type')->toString();
        $id = (int) $request->integer('id');

        if (! auth()->check()) {
            session(['intended_purchase' => ['type' => $type, 'id' => $id]]);

            return redirect()->route('purchase.identify');
        }

        $this->cartService->add($type, $id);

        return redirect()->route('checkout.index');
    }

    public function destroy(int $cartItemId): RedirectResponse
    {
        $this->cartService->remove($cartItemId);

        return back()->with('status', 'Item removed from your cart.');
    }
}
