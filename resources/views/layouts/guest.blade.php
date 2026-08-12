<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Welcome') | EduSphere</title>
    <link rel="icon" type="image/png" href="https://picsum.photos/seed/edusphere/32/32">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body style="background: var(--gradient-hero); min-height: 100vh;">
<div class="container">
    <div class="section-padding" style="padding-top: 60px;">
        <div class="text-center mb-4">
            <a href="{{ route('home') }}" class="navbar-brand-custom d-inline-flex">
                <span class="logo-mark"><i class="bi bi-mortarboard-fill"></i></span> EduSphere
            </a>
        </div>

        @include('partials.flash-messages')

        {{ $slot ?? '' }}
        @yield('content')
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
