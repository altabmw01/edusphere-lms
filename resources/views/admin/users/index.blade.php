@extends('layouts.app')

@section('title', 'Users')
@section('page-title', 'Users')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <form method="GET" class="d-flex gap-2">
        <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-custom" placeholder="Search by name..." style="width:240px;">
        <select name="role" class="form-select form-control-custom" onchange="this.form.submit()">
            <option value="">All Roles</option>
            <option value="admin" @selected(request('role') === 'admin')>Admin</option>
            <option value="manager" @selected(request('role') === 'manager')>Manager</option>
            <option value="teacher" @selected(request('role') === 'teacher')>Teacher</option>
            <option value="student" @selected(request('role') === 'student')>Student</option>
        </select>
        <button class="btn btn-outline-brand" type="submit">Filter</button>
    </form>
    <a href="{{ route('admin.users.create') }}" class="btn btn-brand"><i class="bi bi-plus"></i> New Staff Account</a>
</div>

<div class="table-brand">
    <table class="table mb-0">
        <thead><tr><th>User</th><th>Role</th><th>Joined</th><th>Status</th><th></th></tr></thead>
        <tbody>
            @forelse($users as $user)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ $user->avatarUrl() }}" class="avatar-sm" alt="{{ $user->name }}">
                            <div><span class="d-block small fw-semibold">{{ $user->name }}</span><span class="text-muted small">{{ $user->email }}</span></div>
                        </div>
                    </td>
                    <td class="text-capitalize">{{ $user->role }}</td>
                    <td class="text-muted small">{{ $user->created_at->format('M d, Y') }}</td>
                    <td><x-status-badge :status="$user->is_active ? 'active' : 'inactive'" /></td>
                    <td class="text-end">
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-icon-circle"><i class="bi bi-pencil"></i></a>
                        @if($user->id !== auth()->id())
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline" data-confirm="Delete this user account?">
                            @csrf @method('DELETE')
                            <button class="btn btn-icon-circle text-danger"><i class="bi bi-trash3"></i></button>
                        </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-5">No users found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $users->links() }}</div>
@endsection
