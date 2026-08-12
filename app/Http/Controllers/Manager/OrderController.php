<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(protected OrderRepositoryInterface $orders)
    {
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Order::class);

        return view('manager.orders.index', [
            'orders' => $this->orders->paginateAll($request->only(['status', 'payment_status'])),
        ]);
    }

    public function show(Order $order): View
    {
        $this->authorize('view', $order);

        return view('manager.orders.show', ['order' => $order->load('items', 'user', 'coupon')]);
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $this->authorize('update', $order);

        $data = $request->validate([
            'status' => ['required', 'in:pending,processing,completed,cancelled,refunded'],
            'payment_status' => ['required', 'in:pending,paid,failed,refunded'],
        ]);

        $order->update($data);

        if ($data['status'] === 'cancelled') {
            $order->update(['cancelled_at' => now()]);
        }

        return back()->with('status', 'Order updated.');
    }

    public function downloadInvoice(Order $order)
    {
        $this->authorize('view', $order);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('invoices.template', ['order' => $order->load('items')]);

        return $pdf->download("invoice-{$order->order_number}.pdf");
    }
}
