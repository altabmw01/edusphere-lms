@extends('layouts.app')

@section('title', 'FAQs')
@section('page-title', 'FAQs')

@section('content')
<div class="d-flex justify-content-end mb-4">
    <button class="btn btn-brand" data-bs-toggle="modal" data-bs-target="#createFaqModal"><i class="bi bi-plus"></i> New FAQ</button>
</div>

<div class="table-brand">
    <table class="table mb-0">
        <thead><tr><th>Question</th><th>Category</th><th>Order</th><th>Status</th><th></th></tr></thead>
        <tbody>
            @forelse($faqs as $faq)
                <tr>
                    <td>{{ \Illuminate\Support\Str::limit($faq->question, 60) }}</td>
                    <td>{{ $faq->category }}</td>
                    <td>{{ $faq->sort_order }}</td>
                    <td><x-status-badge :status="$faq->status ? 'active' : 'inactive'" /></td>
                    <td class="text-end">
                        <button class="btn btn-icon-circle" data-bs-toggle="modal" data-bs-target="#editFaqModal{{ $faq->id }}"><i class="bi bi-pencil"></i></button>
                        <form action="{{ route('admin.faqs.destroy', $faq) }}" method="POST" class="d-inline" data-confirm="Delete this FAQ?">
                            @csrf @method('DELETE')
                            <button class="btn btn-icon-circle text-danger"><i class="bi bi-trash3"></i></button>
                        </form>
                    </td>
                </tr>

                <div class="modal fade" id="editFaqModal{{ $faq->id }}" tabindex="-1">
                    <div class="modal-dialog modal-lg"><div class="modal-content" style="border-radius:var(--radius-lg);">
                        <div class="modal-body p-4">
                            <h5 class="mb-3">Edit FAQ</h5>
                            <form method="POST" action="{{ route('admin.faqs.update', $faq) }}">
                                @csrf @method('PUT')
                                <x-form.input name="question" label="Question" :value="$faq->question" required />
                                <x-form.textarea name="answer" label="Answer" rows="4" :value="$faq->answer" required />
                                <div class="row">
                                    <div class="col-md-6"><x-form.input name="category" label="Category" :value="$faq->category" /></div>
                                    <div class="col-md-6"><x-form.input name="sort_order" type="number" label="Sort Order" :value="$faq->sort_order" /></div>
                                </div>
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="status" value="1" id="fstatus{{ $faq->id }}" @checked($faq->status)>
                                    <label class="form-check-label small" for="fstatus{{ $faq->id }}">Published</label>
                                </div>
                                <button class="btn btn-brand w-100">Save Changes</button>
                            </form>
                        </div>
                    </div></div>
                </div>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-5">No FAQs yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $faqs->links() }}</div>

<div class="modal fade" id="createFaqModal" tabindex="-1">
    <div class="modal-dialog modal-lg"><div class="modal-content" style="border-radius:var(--radius-lg);">
        <div class="modal-body p-4">
            <h5 class="mb-3">New FAQ</h5>
            <form method="POST" action="{{ route('admin.faqs.store') }}">
                @csrf
                <x-form.input name="question" label="Question" required />
                <x-form.textarea name="answer" label="Answer" rows="4" required />
                <div class="row">
                    <div class="col-md-6"><x-form.input name="category" label="Category" value="Getting Started" /></div>
                    <div class="col-md-6"><x-form.input name="sort_order" type="number" label="Sort Order" value="0" /></div>
                </div>
                <button class="btn btn-brand w-100">Add FAQ</button>
            </form>
        </div>
    </div></div>
</div>
@endsection
