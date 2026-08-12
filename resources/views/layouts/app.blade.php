<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') | EduSphere</title>
    <link rel="icon" type="image/png" href="https://picsum.photos/seed/edusphere/32/32">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>
<body>
<div class="dash-wrapper">
    <aside class="dash-sidebar">
        <a href="{{ route('home') }}" class="brand">
            <span class="logo-mark"><i class="bi bi-mortarboard-fill"></i></span>
            EduSphere
        </a>
        @auth
            @include('partials.sidebar-' . auth()->user()->role)
        @endauth
    </aside>

    <div class="dash-main">
        <header class="dash-topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-icon-circle sidebar-toggle-btn" aria-label="Toggle sidebar"><i class="bi bi-list fs-5"></i></button>
                <h5 class="mb-0">@yield('page-title', 'Dashboard')</h5>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="dropdown">
                    <button class="btn btn-icon-circle position-relative" data-bs-toggle="dropdown" aria-label="Notifications">
                        <i class="bi bi-bell"></i>
                        @auth
                            @php($unread = auth()->user()->unreadNotifications()->count())
                            @if($unread > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:10px;">{{ $unread }}</span>
                            @endif
                        @endauth
                    </button>
                    <div class="dropdown-menu dropdown-menu-end p-2" style="width: 320px;">
                        <h6 class="dropdown-header">Notifications</h6>
                        @auth
                            @forelse(auth()->user()->notifications()->latest()->limit(5)->get() as $notification)
                                <div class="dropdown-item small text-wrap py-2 border-bottom">
                                    {{ $notification->data['message'] ?? 'New notification' }}
                                    <div class="text-muted" style="font-size:11px;">{{ $notification->created_at->diffForHumans() }}</div>
                                </div>
                            @empty
                                <p class="text-muted small px-2 mb-0">You're all caught up.</p>
                            @endforelse
                        @endauth
                    </div>
                </div>
                <div class="dropdown">
                    <button class="btn d-flex align-items-center gap-2 border-0 bg-transparent" data-bs-toggle="dropdown">
                        <img src="{{ auth()->user()->avatarUrl() }}" class="avatar-sm" alt="{{ auth()->user()->name }}">
                        <span class="d-none d-md-inline small fw-semibold">{{ auth()->user()->name }}</span>
                        <i class="bi bi-chevron-down small"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i> My Profile</a>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i> Logout</button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <main class="dash-content">
            @include('partials.flash-messages')
            @yield('content')
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/app.js') }}"></script>
@stack('scripts')
</body>
</html>
