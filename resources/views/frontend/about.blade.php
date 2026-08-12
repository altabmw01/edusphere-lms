@extends('layouts.frontend')

@section('title', 'About Us')

@section('content')
<header class="section-padding pb-0" style="background: var(--gradient-hero); padding-top:60px;">
    <div class="container text-center" data-aos="fade-up">
        <h1 class="section-title">About EduSphere</h1>
        <p class="section-subtitle mx-auto">Helping people learn better, together.</p>
    </div>
</header>

<section class="section-padding">
    <div class="container">
        <div class="row align-items-center g-5 mb-5">
            <div class="col-lg-6" data-aos="fade-right">
                <span class="eyebrow">Our Story</span>
                <h2 class="section-title">A platform built for real learners</h2>
                <p>Founded to make high-quality education accessible, EduSphere has grown into a platform offering courses and books across dozens of in-demand fields, taught by instructors with real, hands-on industry experience.</p>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <img src="https://picsum.photos/seed/aboutteam/600/440" class="img-fluid rounded-4 shadow-sm" alt="EduSphere team">
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-6" data-aos="fade-up">
                <div class="feature-card h-100"><div class="feature-icon"><i class="bi bi-bullseye"></i></div><h5>Our Mission</h5><p class="mb-0">To make high-quality, practical education accessible to anyone with the drive to learn.</p></div>
            </div>
            <div class="col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="feature-card h-100"><div class="feature-icon"><i class="bi bi-eye"></i></div><h5>Our Vision</h5><p class="mb-0">A world where learning never stops, and everyone has the tools to build the career they want.</p></div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-4 col-md-6" data-aos="fade-up"><div class="feature-card"><div class="feature-icon"><i class="bi bi-person-video3"></i></div><h5>Expert Teachers</h5><p class="mb-0">Learn from vetted professionals with real industry experience.</p></div></div>
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100"><div class="feature-card"><div class="feature-icon"><i class="bi bi-infinity"></i></div><h5>Lifetime Access</h5><p class="mb-0">Learn at your own pace, revisit materials whenever you need.</p></div></div>
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200"><div class="feature-card"><div class="feature-icon"><i class="bi bi-patch-check"></i></div><h5>Certificate</h5><p class="mb-0">Earn recognized certificates to showcase your new skills.</p></div></div>
        </div>
    </div>
</section>
@endsection
