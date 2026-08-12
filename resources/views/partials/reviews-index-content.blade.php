@php($prefix = request()->routeIs('manager.*') ? 'manager' : 'admin')

<form method="GET" class="d-flex gap-2 mb-4">
    <select name="status" class="form-select form-control-custom" onchange="this.form.submit()" style="width:220px;">
        <option value="">All Statuses</option>
        <option value="pending" @selected(request('status') === 'pending')>Pending</option>
        <option value="approved" @selected(request('status') === 'approved')>Approved</option>
        <option value="rejected" @selected(request('status') === 'rejected')>Rejected</option>
    </select>
</form>

<div class="row g-3">
    @forelse($reviews as $review)
        <div class="col-md-6">
            <div class="filter-card h-100">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="d-flex align-items-center gap-2">
                        <img src="{{ $review->user->avatarUrl() }}" class="avatar-sm" alt="{{ $review->user->name }}">
                        <div><span class="d-block small fw-semibold">{{ $review->user->name }}</span><span class="rating-stars small">{!! star_rating($review->rating) !!}</span></div>
                    </div>
                    <x-status-badge :status="$review->status" />
                </div>
                <p class="small text-muted mb-1"><strong>{{ class_basename($review->reviewable_type) }}:</strong> {{ $review->reviewable?->title }}</p>
                <p class="mb-3">{{ $review->comment }}</p>

                @if($review->reply)
                    <div class="bg-light rounded-3 p-2 mb-3 small"><strong>Reply:</strong> {{ $review->reply }}</div>
                @endif

                <div class="d-flex gap-2 flex-wrap">
                    @if($review->status !== 'approved')
                        <form action="{{ route($prefix.'.reviews.approve', $review) }}" method="POST">
                            @csrf @method('PUT')
                            <button class="btn btn-sm-pill btn-outline-brand"><i class="bi bi-check"></i> Approve</button>
                        </form>
                    @endif
                    @if($review->status !== 'rejected')
                        <form action="{{ route($prefix.'.reviews.reject', $review) }}" method="POST">
                            @csrf @method('PUT')
                            <button class="btn btn-sm-pill btn-outline-danger"><i class="bi bi-x"></i> Reject</button>
                        </form>
                    @endif
                    <button class="btn btn-sm-pill btn-brand" data-bs-toggle="modal" data-bs-target="#replyModal{{ $review->id }}"><i class="bi bi-reply"></i> Reply</button>
                </div>
            </div>
        </div>

        <div class="modal fade" id="replyModal{{ $review->id }}" tabindex="-1">
            <div class="modal-dialog"><div class="modal-content" style="border-radius:var(--radius-lg);">
                <div class="modal-body p-4">
                    <h6 class="mb-3">Reply to {{ $review->user->name }}</h6>
                    <form action="{{ route($prefix.'.reviews.reply', $review) }}" method="POST">
                        @csrf
                        <textarea name="reply" class="form-control form-control-custom mb-3" rows="3" required>{{ $review->reply }}</textarea>
                        <button class="btn btn-brand w-100">Post Reply</button>
                    </form>
                </div>
            </div></div>
        </div>
    @empty
        <div class="empty-state"><i class="bi bi-star"></i><p>No reviews found.</p></div>
    @endforelse
</div>
<div class="mt-4">{{ $reviews->links() }}</div>
