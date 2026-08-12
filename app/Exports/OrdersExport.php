<?php

namespace App\Exports;

use App\Models\Order;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OrdersExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(protected array $filters = [])
    {
    }

    public function collection(): Collection
    {
        return Order::query()
            ->with('user')
            ->when(! empty($this->filters['from']), fn ($q) => $q->whereDate('created_at', '>=', $this->filters['from']))
            ->when(! empty($this->filters['to']), fn ($q) => $q->whereDate('created_at', '<=', $this->filters['to']))
            ->latest()
            ->get();
    }

    public function headings(): array
    {
        return ['Order #', 'Customer', 'Email', 'Grand Total', 'Payment Status', 'Order Status', 'Date'];
    }

    public function map($order): array
    {
        return [
            $order->order_number,
            $order->user->name,
            $order->billing_email,
            $order->grand_total,
            $order->payment_status,
            $order->status,
            $order->created_at->format('Y-m-d H:i'),
        ];
    }
}
