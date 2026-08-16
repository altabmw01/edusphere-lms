@php($r = fn($name) => request()->routeIs($name) ? 'active' : '')

<nav class="navbar navbar-expand-lg main-nav sticky-top py-3">
    <div class="container">
        <a class="navbar-brand-custom" href="{{ route('home') }}">
            <span class="logo-mark"><i class="bi bi-mortarboard-fill"></i></span> EduSphere
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
            <i class="bi bi-list fs-2"></i>
        </button>
        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link {{ $r('home') }}" href="{{ route('home') }}">Home</a></li>
                <li class="nav-item"><a class="nav-link {{ $r('about') }}" href="{{ route('about') }}">About</a></li>
                <li class="nav-item"><a class="nav-link {{ $r('courses.*') }}" href="{{ route('courses.index') }}">Courses</a></li>
                <li class="nav-item"><a class="nav-link {{ $r('books.*') }}" href="{{ route('books.index') }}">Books</a></li>
                <li class="nav-item"><a class="nav-link {{ $r('faq') }}" href="{{ route('faq') }}">FAQ</a></li>
                <li class="nav-item"><a class="nav-link {{ $r('contact.*') }}" href="{{ route('contact.index') }}">Contact</a></li>
            </ul>
            <div class="d-flex align-items-center gap-2">

                @auth
                    <div class="dropdown">
                        <button class="btn d-flex align-items-center gap-2 border-0 bg-transparent" data-bs-toggle="dropdown">
                            <img src="{{ auth()->user()->avatarUrl() }}" class="avatar-sm" alt="{{ auth()->user()->name }}">
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            @php($dashRoute = match(auth()->user()->role) {
                                'admin' => route('admin.dashboard'),
                                'teacher' => route('teacher.dashboard'),
                                'manager' => route('manager.dashboard'),
                                default => route('student.dashboard'),
                            })
                            <a class="dropdown-item" href="{{ $dashRoute }}"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
                            <a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i> My Profile</a>
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i> Logout</button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-brand btn-sm-pill d-none d-lg-inline-block">Login</a>
                    <a href="{{ route('register') }}" class="btn btn-brand btn-sm-pill d-none d-lg-inline-block">Register</a>
                @endauth
            </div>
        </div>
    </div>
</nav>
