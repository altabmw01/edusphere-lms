<footer class="site-footer">
    <div class="container">
        <div class="row g-4 pb-4">
            <div class="col-lg-4 col-md-6">
                <a class="navbar-brand-custom mb-3 d-inline-flex" href="{{ route('home') }}" style="color:#fff !important;">
                    <span class="logo-mark"><i class="bi bi-mortarboard-fill"></i></span> EduSphere
                </a>
                <p class="small mb-4">{{ setting('seo_description', "EduSphere helps ambitious learners master new skills through expert-led courses and a curated library of digital books.") }}</p>
                <div class="d-flex gap-2">
                    @if(setting('social_facebook'))<a href="{{ setting('social_facebook') }}" class="btn-icon-circle" target="_blank"><i class="bi bi-facebook"></i></a>@endif
                    @if(setting('social_twitter'))<a href="{{ setting('social_twitter') }}" class="btn-icon-circle" target="_blank"><i class="bi bi-twitter-x"></i></a>@endif
                    @if(setting('social_instagram'))<a href="{{ setting('social_instagram') }}" class="btn-icon-circle" target="_blank"><i class="bi bi-instagram"></i></a>@endif
                </div>
            </div>
            <div class="col-lg-2 col-md-6 col-6">
                <h6>Quick Links</h6>
                <ul class="list-unstyled">
                    <li><a href="{{ route('about') }}">About Us</a></li>
                    <li><a href="{{ route('courses.index') }}">Courses</a></li>
                    <li><a href="{{ route('books.index') }}">Books</a></li>
                    <li><a href="{{ route('faq') }}">FAQ</a></li>
                    <li><a href="{{ route('contact.index') }}">Contact</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-md-6 col-6">
                <h6>Support</h6>
                <ul class="list-unstyled">
                    <li><a href="{{ route('contact.index') }}">Help Center</a></li>
                    <li><a href="{{ route('faq') }}">FAQs</a></li>
                    <li><a href="{{ route('login') }}">My Account</a></li>
                    <li><a href="{{ route('cart.index') }}">Cart</a></li>
                </ul>
            </div>
            <div class="col-lg-4 col-md-6">
                <h6>Newsletter</h6>
                <p class="small">Get weekly tips, new course drops, and exclusive discounts straight to your inbox.</p>
                <form method="POST" action="{{ route('newsletter.store') }}" class="d-flex">
                    @csrf
                    <input type="email" name="email" class="form-control form-control-custom" placeholder="Your email address" required style="border-radius: var(--radius-pill) 0 0 var(--radius-pill); background:#1E293B; border-color:#334155; color:#fff;">
                    <button class="btn btn-brand" style="border-radius: 0 999px 999px 0;">Join</button>
                </form>
            </div>
        </div>
        <div class="footer-bottom d-flex flex-wrap justify-content-between align-items-center">
            <p class="mb-0">&copy; {{ now()->year }} {{ setting('site_name', 'EduSphere') }}. All rights reserved.</p>
            <div class="d-flex gap-3">
                <a href="{{ route('login') }}">Login</a>
                <a href="{{ route('register') }}">Register</a>
                <a href="{{ route('contact.index') }}">Contact</a>
            </div>
        </div>
    </div>
</footer>
