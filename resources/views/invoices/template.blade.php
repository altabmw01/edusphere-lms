<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    body { font-family: DejaVu Sans, sans-serif; color: #1E293B; font-size: 13px; }
    .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 3px solid #2563EB; padding-bottom: 16px; margin-bottom: 24px; }
    .brand { font-size: 22px; font-weight: bold; color: #2563EB; }
    .invoice-title { text-align: right; }
    .invoice-title h2 { margin: 0; color: #1E293B; }
    .meta-table { width: 100%; margin-bottom: 24px; }
    .meta-table td { vertical-align: top; padding: 4px 0; }
    .badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 11px; background: #DCFCE7; color: #15803D; }
    table.items { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    table.items th { background: #F8FAFC; text-align: left; padding: 10px; font-size: 11px; text-transform: uppercase; color: #64748B; border-bottom: 1px solid #E2E8F0; }
    table.items td { padding: 10px; border-bottom: 1px solid #E2E8F0; }
    .totals { width: 300px; margin-left: auto; }
    .totals td { padding: 6px 0; }
    .totals .grand { font-weight: bold; font-size: 16px; color: #2563EB; border-top: 2px solid #1E293B; padding-top: 10px; }
    .footer { margin-top: 40px; text-align: center; color: #94A3B8; font-size: 11px; }
</style>
</head>
<body>
    <div class="header">
        <div class="brand">🎓 EduSphere</div>
        <div class="invoice-title">
            <h2>INVOICE</h2>
            <span class="badge">{{ strtoupper($order->payment_status) }}</span>
        </div>
    </div>

    <table class="meta-table">
        <tr>
            <td width="50%">
                <strong>Billed To</strong><br>
                {{ $order->billing_name }}<br>
                {{ $order->billing_email }}<br>
                {{ $order->billing_phone }}<br>
                @if($order->address){{ $order->address }}@endif 
                @if($order->union), {{ $order->union }}@endif 
                @if($order->thana), {{ $order->thana }}@endif 
                @if($order->district), {{ $order->district }}@endif 
                @if($order->division), {{ $order->division }}@endif 
                @if($order->zip), {{ $order->zip }}@endif
                @if($order->country), {{ $order->country }}@endif
            </td>
            <td width="50%" style="text-align:right;">
                <strong>Invoice #{{ $order->order_number }}</strong><br>
                Date: {{ $order->created_at->format('M d, Y') }}<br>
                Payment Method: {{ strtoupper($order->payment_method) }}<br>
                Status: {{ ucfirst($order->status) }}
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr><th>Item</th><th>Type</th><th>Qty</th><th style="text-align:right;">Price</th><th style="text-align:right;">Total</th></tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->title }}</td>
                    <td>{{ class_basename($item->purchasable_type) }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td style="text-align:right;">{{ 'TK '.$item->price }}</td>
                    <td style="text-align:right;">{{ 'TK '.$item->line_total }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr><td>Subtotal</td><td style="text-align:right;">{{ 'TK '.$order->subtotal }}</td></tr>
        <tr><td>Discount</td><td style="text-align:right;">-{{ 'TK '.$order->discount_total }}</td></tr>
        <tr><td>Tax</td><td style="text-align:right;">{{ 'TK '.$order->tax_total }}</td></tr>
        <tr><td>Shipping</td><td style="text-align:right;">{{ 'TK '.$order->shipping_total }}</td></tr>
        <tr class="grand"><td>Grand Total</td><td style="text-align:right;">{{ 'TK '.$order->grand_total }}</td></tr>
    </table>

    <div class="footer">Thank you for learning with EduSphere. Questions? Contact support@edusphere.test</div>
</body>
</html>
