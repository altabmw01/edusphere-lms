<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\CheckoutService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * SSLCommerz payment gateway integration.
 *
 * Configure SSLCOMMERZ_STORE_ID / SSLCOMMERZ_STORE_PASSWORD in .env (see config/lms.php).
 * Uses the sandbox endpoint by default; set SSLCOMMERZ_SANDBOX=false for production.
 */
class PaymentController extends Controller
{
    public function __construct(protected CheckoutService $checkoutService)
    {
    }

    protected function baseUrl(): string
    {
        return config('lms.sslcommerz.sandbox')
            ? 'https://sandbox.sslcommerz.com'
            : 'https://securepay.sslcommerz.com';
    }

    public function init(Order $order): RedirectResponse
    {
        abort_unless($order->user_id === auth()->id(), 403);
        abort_if($order->isPaid(), 400, 'This order has already been paid.');

        $payload = [
            'store_id' => config('lms.sslcommerz.store_id'),
            'store_passwd' => config('lms.sslcommerz.store_password'),
            'total_amount' => $order->grand_total,
            'currency' => config('lms.currency_code', 'BDT'),
            'tran_id' => $order->order_number,
            'success_url' => route('payment.sslcommerz.success'),
            'fail_url' => route('payment.sslcommerz.fail'),
            'cancel_url' => route('payment.sslcommerz.cancel'),
            'ipn_url' => route('payment.sslcommerz.ipn'),
            'cus_name' => $order->billing_name,
            'cus_email' => $order->billing_email,
            'cus_phone' => $order->billing_phone,
            'cus_add1' => $order->address,
            'cus_city' => $order->district,
            'cus_country' => $order->country,
            'shipping_method' => 'NO',
            'product_name' => 'EduSphere Order ' . $order->order_number,
            'product_category' => 'Digital',
            'product_profile' => 'general',
        ];

        $response = Http::asForm()->post($this->baseUrl() . '/gwprocess/v4/api.php', $payload)->json();

        if (($response['status'] ?? null) !== 'SUCCESS') {
            Log::warning('SSLCommerz session initiation failed', ['order' => $order->order_number, 'response' => $response]);

            return redirect()->route('checkout.index')->with('status', 'Unable to start the payment session. Please try again.');
        }

        return redirect()->away($response['GatewayPageURL']);
    }

    public function success(Request $request): RedirectResponse
    {
        $order = Order::where('order_number', $request->input('tran_id'))->firstOrFail();

        if (! $this->validateTransaction($request->input('val_id'))) {
            return redirect()->route('student.orders.show', $order)->with('status', 'Payment verification failed.');
        }

        $this->checkoutService->markPaidAndFulfill($order, $request->input('val_id'));

        return redirect()->route('student.orders.show', $order)->with('status', 'Payment successful — your order is now active!');
    }

    public function fail(Request $request): RedirectResponse
    {
        $order = Order::where('order_number', $request->input('tran_id'))->first();
        $order?->update(['payment_status' => 'failed']);

        return redirect()->route('cart.index')->with('status', 'Payment failed. Please try again.');
    }

    public function cancel(Request $request): RedirectResponse
    {
        $order = Order::where('order_number', $request->input('tran_id'))->first();
        $order?->update(['status' => 'cancelled', 'cancelled_at' => now()]);

        return redirect()->route('cart.index')->with('status', 'Payment was cancelled.');
    }

    /**
     * Instant Payment Notification webhook — SSLCommerz calls this server-to-server,
     * independent of the customer's browser redirect, so it is the source of truth.
     */
    public function ipn(Request $request): \Illuminate\Http\JsonResponse
    {
        $order = Order::where('order_number', $request->input('tran_id'))->first();

        if (! $order) {
            return response()->json(['status' => 'error', 'message' => 'Order not found'], 404);
        }

        if ($this->validateTransaction($request->input('val_id')) && ! $order->isPaid()) {
            $this->checkoutService->markPaidAndFulfill($order, $request->input('val_id'));
        }

        return response()->json(['status' => 'ok']);
    }

    protected function validateTransaction(?string $valId): bool
    {
        if (! $valId) {
            return false;
        }

        $response = Http::get($this->baseUrl() . '/validator/api/validationserverAPI.php', [
            'val_id' => $valId,
            'store_id' => config('lms.sslcommerz.store_id'),
            'store_passwd' => config('lms.sslcommerz.store_password'),
            'format' => 'json',
        ])->json();

        return in_array($response['status'] ?? null, ['VALID', 'VALIDATED'], true);
    }
}
