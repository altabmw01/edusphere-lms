@extends('layouts.app')
@section('title', 'Coupons')
@section('page-title', 'Coupons')
@section('content')
<div class="d-flex justify-content-end mb-4">
    <a href="{{ route('manager.coupons.create') }}" class="btn btn-brand"><i class="bi bi-plus"></i> New Coupon</a>
</div>
<div class="table-brand">
    <table class="table mb-0">
        <thead><tr><th>Code</th><th>Type</th><th>Value</th><th>Used</th><th>Expires</th><th>Status</th><th></th></tr></thead>
        <tbody>
            @forelse($coupons as $coupon)
                <tr>
                    <td><code>{{ $coupon->code }}</code></td>
                    <td class="text-capitalize">{{ $coupon->type }}</td>
                    <td>{{ $coupon->type === 'percentage' ? $coupon->value.'%' : money($coupon->value) }}</td>
                    <td>{{ $coupon->used_count }} / {{ $coupon->usage_limit ?? '∞' }}</td>
                    <td>{{ $coupon->expires_at?->format('M d, Y') ?? 'Never' }}</td>
                    <td><x-status-badge :status="$coupon->status ? 'active' : 'inactive'" /></td>
                    <td class="text-end">
                        <a href="{{ route('manager.coupons.edit', $coupon) }}" class="btn btn-icon-circle"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('manager.coupons.destroy', $coupon) }}" method="POST" class="d-inline" data-confirm="Delete this coupon?">
                            @csrf @method('DELETE')
                            <button class="btn btn-icon-circle text-danger"><i class="bi bi-trash3"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-5">No coupons yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $coupons->links() }}</div>
@endsection
