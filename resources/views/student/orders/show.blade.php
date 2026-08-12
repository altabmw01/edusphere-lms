@extends('layouts.app')
@section('title', 'Order '.$order->order_number)
@section('page-title', 'Order Details')
@section('content')
@include('partials.order-detail', [
    'order' => $order,
    'canManage' => false,
    'invoiceRoute' => route('student.orders.invoice', $order),
])
@endsection
