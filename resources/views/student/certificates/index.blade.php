@extends('layouts.app')

@section('title', 'Certificates')
@section('page-title', 'My Certificates')

@section('content')
<div class="row g-4">
    @forelse($certificates as $certificate)
        <div class="col-lg-4 col-md-6">
            <div class="filter-card text-center h-100">
                <div class="feature-icon mx-auto mb-3" style="width:64px;height:64px;font-size:28px;"><i class="bi bi-patch-check-fill"></i></div>
                <h6 class="mb-1">{{ $certificate->course->title }}</h6>
                <p class="small text-muted mb-3">Issued {{ $certificate->issued_at->format('M d, Y') }}</p>
                <p class="small text-muted mb-3">Certificate No. {{ $certificate->certificate_number }}</p>
                <a href="{{ route('student.certificates.download', $certificate) }}" class="btn btn-brand w-100"><i class="bi bi-download me-1"></i> Download PDF</a>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="empty-state"><i class="bi bi-patch-check"></i><p>Complete a course to earn your first certificate.</p><a href="{{ route('student.my-courses.index') }}" class="btn btn-brand">My Courses</a></div>
        </div>
    @endforelse
</div>
<div class="mt-4">{{ $certificates->links() }}</div>
@endsection
