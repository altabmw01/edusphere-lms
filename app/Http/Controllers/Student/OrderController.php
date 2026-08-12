<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OrderController extends Controller
{
    use AuthorizesRequests;

    public function index(): View
    {
        $orders = Order::where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('student.orders.index', [
            'orders' => $orders
        ]);
    }

    public function show(Order $order): View
    {
        $this->authorize('view', $order);

        return view('student.orders.show', [
            'order' => $order->load('items', 'coupon')
        ]);
    }

    public function cancel(Order $order): RedirectResponse
    {
        $this->authorize('cancel', $order);

        $order->update([
            'status' => 'cancelled',
            'cancelled_at' => now()
        ]);

        return back()->with('status', 'Order cancelled.');
    }

    public function downloadInvoice(Order $order)
    {
        $this->authorize('view', $order);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'invoices.template',
            ['order' => $order->load('items')]
        );

        return $pdf->download(
            "invoice-{$order->order_number}.pdf"
        );
    }
}