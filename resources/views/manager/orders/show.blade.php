@extends('layouts.app')
@section('title', 'Order '.$order->order_number)
@section('page-title', 'Order Details')
@section('content')
@include('partials.order-detail', [
    'order' => $order,
    'canManage' => true,
    'invoiceRoute' => route('manager.orders.invoice', $order),
    'updateRoute' => route('manager.orders.update', $order),
])
@endsection
