@extends('layouts.app')
@section('title', 'Order '.$order->order_number)
@section('page-title', 'Order Details')
@section('content')
@include('partials.order-detail', [
    'order' => $order,
    'canManage' => true,
    'invoiceRoute' => route('admin.orders.invoice', $order),
    'updateRoute' => route('admin.orders.update', $order),
])
@endsection
