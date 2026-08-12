@php($prefix = request()->routeIs('manager.*') ? 'manager' : 'admin')

<div class="d-flex justify-content-between align-items-center mb-4">
    <form method="GET" class="d-flex gap-2">
        <select name="type" class="form-select form-control-custom" onchange="this.form.submit()">
            <option value="">All Types</option>
            <option value="course" @selected(request('type') === 'course')>Course Categories</option>
            <option value="book" @selected(request('type') === 'book')>Book Categories</option>
        </select>
    </form>
    <button class="btn btn-brand" data-bs-toggle="modal" data-bs-target="#createCategoryModal"><i class="bi bi-plus"></i> New Category</button>
</div>

<div class="table-brand">
    <table class="table mb-0">
        <thead><tr><th>Name</th><th>Type</th><th>Courses/Books</th><th>Order</th><th>Status</th><th></th></tr></thead>
        <tbody>
            @forelse($categories as $category)
                <tr>
                    <td><i class="bi {{ $category->icon }} me-2" style="color:{{ $category->color }};"></i>{{ $category->name }}</td>
                    <td class="text-capitalize">{{ $category->type }}</td>
                    <td>{{ $category->type === 'course' ? $category->courses()->count() : $category->books()->count() }}</td>
                    <td>{{ $category->sort_order }}</td>
                    <td><x-status-badge :status="$category->status ? 'active' : 'inactive'" /></td>
                    <td class="text-end">
                        <button class="btn btn-icon-circle" data-bs-toggle="modal" data-bs-target="#editCategoryModal{{ $category->id }}"><i class="bi bi-pencil"></i></button>
                        <form action="{{ route($prefix.'.categories.destroy', $category) }}" method="POST" class="d-inline" data-confirm="Delete this category?">
                            @csrf @method('DELETE')
                            <button class="btn btn-icon-circle text-danger"><i class="bi bi-trash3"></i></button>
                        </form>
                    </td>
                </tr>

                <div class="modal fade" id="editCategoryModal{{ $category->id }}" tabindex="-1">
                    <div class="modal-dialog"><div class="modal-content" style="border-radius:var(--radius-lg);">
                        <div class="modal-body p-4">
                            <h5 class="mb-3">Edit Category</h5>
                            <form method="POST" action="{{ route($prefix.'.categories.update', $category) }}">
                                @csrf @method('PUT')
                                <x-form.input name="name" label="Name" :value="$category->name" required />
                                <x-form.input name="icon" label="Bootstrap Icon Class" :value="$category->icon" hint="e.g. bi-code-slash" />
                                <x-form.input name="color" label="Color (hex)" :value="$category->color" />
                                <x-form.input name="sort_order" type="number" label="Sort Order" :value="$category->sort_order" />
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="status" value="1" id="status{{ $category->id }}" @checked($category->status)>
                                    <label class="form-check-label small" for="status{{ $category->id }}">Active</label>
                                </div>
                                <button class="btn btn-brand w-100">Save Changes</button>
                            </form>
                        </div>
                    </div></div>
                </div>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-5">No categories found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $categories->links() }}</div>

<div class="modal fade" id="createCategoryModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content" style="border-radius:var(--radius-lg);">
        <div class="modal-body p-4">
            <h5 class="mb-3">New Category</h5>
            <form method="POST" action="{{ route($prefix.'.categories.store') }}">
                @csrf
                <x-form.input name="name" label="Name" required />
                <x-form.select name="type" label="Type" :options="['course' => 'Course Category', 'book' => 'Book Category']" required />
                <x-form.input name="icon" label="Bootstrap Icon Class" hint="e.g. bi-code-slash" />
                <x-form.input name="color" label="Color (hex)" value="#2563EB" />
                <x-form.input name="sort_order" type="number" label="Sort Order" value="0" />
                <button class="btn btn-brand w-100">Create Category</button>
            </form>
        </div>
    </div></div>
</div>
