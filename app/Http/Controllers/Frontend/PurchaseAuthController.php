<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Book;
use App\Models\Course;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

/**
 * This project sells one course/book at a time. Clicking "Add to Cart" as a
 * guest routes through here first: enter your mobile number, and depending on
 * whether it belongs to an existing account, either confirm your password or
 * create a new account — then continue straight to checkout with the item
 * you started with.
 */
class PurchaseAuthController extends Controller
{
    public function __construct(protected CartService $cartService)
    {
    }

    public function showIdentify(): View|RedirectResponse
    {
        if (! $item = $this->intendedItem()) {
            return redirect()->route('home')->with('error', 'Please choose a course or book to purchase first.');
        }

        return view('frontend.purchase.identify', ['product' => $this->resolveProduct($item)]);
    }

    public function checkPhone(Request $request): RedirectResponse
    {
        if (! $this->intendedItem()) {
            return redirect()->route('home');
        }

        $request->validate(['phone' => ['required', 'string', 'max:30']]);

        $phone = $request->string('phone')->toString();
        session(['identify_phone' => $phone]);

        if (User::where('phone', $phone)->exists()) {
            return redirect()->route('purchase.password');
        }

        return redirect()->route('purchase.register');
    }

    public function showPassword(): View|RedirectResponse
    {
        if (! $item = $this->intendedItem()) {
            return redirect()->route('home');
        }

        if (! $phone = session('identify_phone')) {
            return redirect()->route('purchase.identify');
        }

        $user = User::where('phone', $phone)->first();

        if (! $user) {
            return redirect()->route('purchase.identify');
        }

        return view('frontend.purchase.password', ['product' => $this->resolveProduct($item), 'identifiedUser' => $user]);
    }

    public function authenticate(Request $request): RedirectResponse
    {
        $phone = session('identify_phone');
        $item = $this->intendedItem();

        if (! $phone || ! $item) {
            return redirect()->route('purchase.identify');
        }

        $request->validate(['password' => ['required', 'string']]);

        $user = User::where('phone', $phone)->first();

        if (! $user || ! Hash::check($request->string('password'), $user->password)) {
            return back()->withErrors(['password' => 'That password is incorrect. Please try again.']);
        }

        if (! $user->is_active) {
            return back()->withErrors(['password' => 'This account has been deactivated. Please contact support.']);
        }

        Auth::login($user, remember: true);
        $user->update(['last_login_at' => now()]);

        $this->completePurchaseHandoff($item);

        return redirect()->route('checkout.index');
    }

    public function showRegister(): View|RedirectResponse
    {
        if (! $item = $this->intendedItem()) {
            return redirect()->route('home');
        }

        return view('frontend.purchase.register', [
            'product' => $this->resolveProduct($item),
            'phone' => session('identify_phone'),
        ]);
    }

    public function register(Request $request): RedirectResponse
    {
        if (! $item = $this->intendedItem()) {
            return redirect()->route('home');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:30', 'unique:users,phone'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
            'role' => User::ROLE_STUDENT,
        ]);

        event(new Registered($user));
        ActivityLog::record('user_registered', "{$user->name} registered while checking out.", $user);

        Auth::login($user, remember: true);

        $this->completePurchaseHandoff($item);

        return redirect()->route('checkout.index');
    }

    /**
     * Add the item the guest originally clicked "Add to Cart" on into their
     * now-authenticated cart, and clear the temporary identify-flow session state.
     */
    protected function completePurchaseHandoff(array $item): void
    {
        $this->cartService->add($item['type'], $item['id']);

        session()->forget(['intended_purchase', 'identify_phone']);
    }

    protected function intendedItem(): ?array
    {
        $item = session('intended_purchase');

        if (! is_array($item) || empty($item['type']) || empty($item['id'])) {
            return null;
        }

        $exists = $item['type'] === 'book'
            ? Book::whereKey($item['id'])->exists()
            : Course::whereKey($item['id'])->exists();

        return $exists ? $item : null;
    }

    protected function resolveProduct(array $item): Course|Book
    {
        return $item['type'] === 'book'
            ? Book::findOrFail($item['id'])
            : Course::findOrFail($item['id']);
    }
}
