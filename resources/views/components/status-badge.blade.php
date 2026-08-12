@props(['status'])

<span class="badge-status status-{{ \Illuminate\Support\Str::slug($status) }}">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
