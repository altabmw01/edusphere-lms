@extends('layouts.frontend')

@section('title', 'FAQ')

@section('content')
<header class="section-padding pb-0" style="background: var(--gradient-hero); padding-top:60px;">
    <div class="container text-center" data-aos="fade-up">
        <h1 class="section-title">Frequently Asked Questions</h1>
        <p class="section-subtitle mx-auto">Answers to the most common questions about EduSphere.</p>
    </div>
</header>

<section class="section-padding">
    <div class="container" style="max-width: 820px;">
        @forelse($faqs as $category => $items)
            <h5 class="mb-3" data-aos="fade-up">{{ $category }}</h5>
            <div class="accordion mb-5" id="faqAccordion{{ \Illuminate\Support\Str::slug($category) }}" data-aos="fade-up">
                @foreach($items as $faq)
                    <div class="card-brand mb-3 overflow-hidden">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq{{ $faq->id }}">
                                {{ $faq->question }}
                            </button>
                        </h2>
                        <div id="faq{{ $faq->id }}" class="accordion-collapse collapse" data-bs-parent="#faqAccordion{{ \Illuminate\Support\Str::slug($category) }}">
                            <div class="accordion-body text-muted">{{ $faq->answer }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        @empty
            <div class="empty-state"><i class="bi bi-question-circle"></i><p>No FAQs published yet.</p></div>
        @endforelse

        <div class="text-center mt-5" data-aos="fade-up">
            <p class="mb-3">Still have questions?</p>
            <a href="{{ route('contact.index') }}" class="btn btn-brand">Contact Support</a>
        </div>
    </div>
</section>
@endsection
