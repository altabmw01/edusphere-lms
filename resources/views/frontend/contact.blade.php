@extends('layouts.frontend')

@section('title', 'Contact Us')

@section('content')
<header class="section-padding pb-0" style="background: var(--gradient-hero); padding-top:60px;">
    <div class="container text-center" data-aos="fade-up">
        <h1 class="section-title">Contact Us</h1>
        <p class="section-subtitle mx-auto">We'd love to hear from you.</p>
    </div>
</header>

<section class="section-padding">
    <div class="container">
        <div class="row g-4 mb-5">
            <div class="col-md-4" data-aos="fade-up">
                <div class="feature-card text-center h-100"><div class="feature-icon mx-auto"><i class="bi bi-geo-alt"></i></div><h6>Our Address</h6><p class="mb-0 small">1200 Market Street, Suite 400<br>San Francisco, CA 94102</p></div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="feature-card text-center h-100"><div class="feature-icon mx-auto"><i class="bi bi-envelope"></i></div><h6>Email Us</h6><p class="mb-0 small">support@edusphere.test</p></div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="feature-card text-center h-100"><div class="feature-icon mx-auto"><i class="bi bi-telephone"></i></div><h6>Call Us</h6><p class="mb-0 small">+1 (555) 010-2030</p></div>
            </div>
        </div>

        <div class="row g-5">
            <div class="col-lg-6" data-aos="fade-right">
                <h4 class="mb-4">Send us a message</h4>
                <form method="POST" action="{{ route('contact.store') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6"><x-form.input name="name" label="Full Name" required /></div>
                        <div class="col-md-6"><x-form.input name="email" type="email" label="Email Address" required /></div>
                        <div class="col-12"><x-form.input name="subject" label="Subject" required /></div>
                        <div class="col-12"><x-form.textarea name="message" label="Message" rows="5" required /></div>
                        <div class="col-12"><button class="btn btn-brand px-4" type="submit">Send Message</button></div>
                    </div>
                </form>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <div class="rounded-4 overflow-hidden shadow-sm" style="height:100%; min-height:380px;">
                    <iframe title="Map" src="https://www.openstreetmap.org/export/embed.html?bbox=-122.45%2C37.75%2C-122.39%2C37.79&amp;layer=mapnik" style="border:0; width:100%; height:100%;" loading="lazy"></iframe>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
