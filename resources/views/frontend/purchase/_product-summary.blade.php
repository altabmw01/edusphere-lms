@php($isBook = $product instanceof \App\Models\Book)

<div class="d-flex align-items-center gap-3 p-3 mb-4 rounded-3" style="background: var(--bg); border: 1px solid var(--border);">
    <img src="{{ $isBook ? $product->cover_url : $product->thumbnail_url }}" alt="{{ $product->title }}" width="64" height="64" class="rounded-3" style="object-fit:cover;">
    <div class="flex-grow-1">
        <span class="badge bg-brand-light text-primary-brand mb-1">{{ $isBook ? 'Book' : 'Course' }}</span>
        <h6 class="mb-0">{{ $product->title }}</h6>
    </div>
    <span class="price-current">{{ money($product->final_price) }}</span>
</div>
