<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    body { font-family: DejaVu Sans, sans-serif; color: #1E293B; font-size: 12px; }
    h2 { color: #2563EB; }
    table { width: 100%; border-collapse: collapse; margin-top: 16px; }
    th { background: #F8FAFC; text-align: left; padding: 8px; font-size: 10px; text-transform: uppercase; color: #64748B; border-bottom: 1px solid #E2E8F0; }
    td { padding: 8px; border-bottom: 1px solid #E2E8F0; }
</style>
</head>
<body>
    <h2>EduSphere — Orders Report</h2>
    <p>Generated {{ now()->format('M d, Y H:i') }}</p>
    <table>
        <thead><tr><th>Order #</th><th>Customer</th><th>Total</th><th>Payment</th><th>Status</th><th>Date</th></tr></thead>
        <tbody>
            @foreach($orders as $order)
                <tr>
                    <td>{{ $order->order_number }}</td>
                    <td>{{ $order->user->name }}</td>
                    <td>{{ money($order->grand_total) }}</td>
                    <td>{{ ucfirst($order->payment_status) }}</td>
                    <td>{{ ucfirst($order->status) }}</td>
                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
