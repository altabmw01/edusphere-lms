@extends('layouts.app')

@section('title', 'Class Link')
@section('page-title', 'Class Link')

@section('content')
@php($hasClassToday = $batch->hasClassToday())
@php($todaysClass = $batch->todaysClass())
@php($nextDate = $batch->nextClassDate())

<div class="mb-3">
    <a href="{{ $backRoute }}" class="text-muted small"><i class="bi bi-arrow-left me-1"></i> Back to {{ $title }}</a>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        @if($hasClassToday)
            <div class="filter-card border-0" style="background: linear-gradient(135deg, #DCFCE7, #F0FDF4);">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <div class="feature-icon mb-0" style="background:#22C55E; color:#fff;"><i class="bi bi-calendar-check"></i></div>
                    <div>
                        <h5 class="mb-0 text-success">You have class today!</h5>
                        <p class="mb-0 text-muted">{{ now()->format('l, F j') }} &middot; {{ $batch->class_start_time?->format('g:i A') }} - {{ $batch->class_end_time?->format('g:i A') }}</p>
                    </div>
                </div>
            </div>
        @else
            <div class="filter-card border-0" style="background: linear-gradient(135deg, #FEF3C7, #FFFBEB);">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <div class="feature-icon mb-0" style="background:var(--accent); color:#fff;"><i class="bi bi-calendar-x"></i></div>
                    <div>
                        <h5 class="mb-0" style="color:#B45309;">You don't have class today</h5>
                        <p class="mb-0 text-muted">
                            @if($nextDate)
                                Your next class is on <strong>{{ $nextDate->format('l, F j') }}</strong> at {{ $batch->class_start_time?->format('g:i A') }}.
                            @else
                                No upcoming class days are scheduled for this batch yet.
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <div class="filter-card {{ $hasClassToday ? '' : 'opacity-75' }}">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0">Meeting Details</h6>
                @if($todaysClass)
                    <span class="badge bg-brand-light text-primary-brand">{{ $todaysClass->linkType?->name }}</span>
                @endif
            </div>

            @if($todaysClass)
                <div class="mb-3">
                    <label class="form-label-custom">Meeting Link</label>
                    <div class="input-group">
                        <input type="text" class="form-control form-control-custom" value="{{ $todaysClass->full_link }}" readonly id="fullLinkInput">
                        <button class="btn btn-outline-brand" type="button" data-copy-target="#fullLinkInput"><i class="bi bi-clipboard"></i> Copy</button>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    @if($todaysClass->metting_code)
                    <div class="col-md-6">
                        <label class="form-label-custom">Meeting Code / ID</label>
                        <div class="input-group">
                            <input type="text" class="form-control form-control-custom" value="{{ $todaysClass->metting_code }}" readonly id="meetingCodeInput">
                            <button class="btn btn-outline-brand" type="button" data-copy-target="#meetingCodeInput"><i class="bi bi-clipboard"></i></button>
                        </div>
                    </div>
                    @endif
                    @if($todaysClass->metting_pass_code)
                    <div class="col-md-6">
                        <label class="form-label-custom">Passcode</label>
                        <div class="input-group">
                            <input type="text" class="form-control form-control-custom" value="{{ $todaysClass->metting_pass_code }}" readonly id="passCodeInput">
                            <button class="btn btn-outline-brand" type="button" data-copy-target="#passCodeInput"><i class="bi bi-clipboard"></i></button>
                        </div>
                    </div>
                    @endif
                </div>

                @if($todaysClass->class_note)
                    <div class="alert alert-info small mb-3"><i class="bi bi-info-circle me-1"></i> {{ $todaysClass->class_note }}</div>
                @endif

                <a href="{{ $todaysClass->full_link }}" target="_blank" rel="noopener" class="btn btn-brand w-100 {{ $hasClassToday ? '' : 'disabled' }}">
                    <i class="bi bi-camera-video me-1"></i> {{ $hasClassToday ? 'Join Class Now' : 'Open Meeting Link (early access)' }}
                </a>
            @else
                <p class="text-muted small mb-0">Your teacher hasn't added a meeting link yet. Please check back closer to your scheduled class time.</p>
            @endif
        </div>
    </div>

    <div class="col-lg-4">
        <div class="filter-card">
            <h6 class="fw-bold mb-3">Batch Schedule</h6>
            <p class="small text-muted mb-1"><i class="bi bi-collection-play me-2"></i>{{ $title }}</p>
            <p class="small text-muted mb-1"><i class="bi bi-person-video3 me-2"></i>{{ $batch->teacher?->name }}</p>
            <p class="small text-muted mb-1"><i class="bi bi-calendar-week me-2"></i>{{ implode(', ', $batch->batch_days ?? []) }}</p>
            <p class="small text-muted mb-0"><i class="bi bi-clock me-2"></i>{{ $batch->class_start_time?->format('g:i A') }} - {{ $batch->class_end_time?->format('g:i A') }}</p>
        </div>
    </div>
</div>
@endsection
