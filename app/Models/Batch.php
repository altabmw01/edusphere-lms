<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Batch extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Canonical day abbreviations, matching Carbon's ->format('D') output.
     * batch_days is stored as a JSON subset of this list, e.g. ["Fri","Sat","Sun","Mon"].
     */
    public const DAYS = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

    protected $fillable = [
        'batchable_type',
        'batchable_id',
        'teacher_id',
        'batch_level_id',
        'batch_number',
        'batch_name',
        'class_start_time',
        'class_end_time',
        'batch_days',
        'weekly_days',
        'batch_started_date',
        'batch_end_date',
        'student_limit',
        'free_or_paid',
        'upcoming_status',
        'hide_batch',
        'added_by',
        'updated_by',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'class_start_time' => 'datetime',
            'class_end_time' => 'datetime',
            'batch_days' => 'array',
            'batch_started_date' => 'date',
            'batch_end_date' => 'date',
            'weekly_days' => 'integer',
            'student_limit' => 'integer',
            'free_or_paid' => 'boolean',
            'upcoming_status' => 'boolean',
            'hide_batch' => 'boolean',
            'status' => 'boolean',
        ];
    }

    public function batchable(): MorphTo
    {
        return $this->morphTo();
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function batchLevel(): BelongsTo
    {
        return $this->belongsTo(BatchLevel::class);
    }

    public function classes(): HasMany
    {
        return $this->hasMany(BatchClass::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    public function bookPurchases(): HasMany
    {
        return $this->hasMany(BookPurchase::class);
    }

    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeForTeacher($query, int $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    public function scopeVisible($query)
    {
        return $query->where('hide_batch', false);
    }

    /** Batches the checkout page should offer a student — active, visible, marked upcoming.
     *  Seat availability (has_seats_available) is checked separately in PHP since it's a
     *  derived count, not a column. */
    /**
     * Batches the checkout page should offer a student — active, visible, and not
     * yet finished (batch_end_date empty or still in the future). Does not require
     * the 'upcoming_status' flag, since that's an optional display marker an admin
     * can easily forget to toggle — requiring it here would silently hide an
     * otherwise perfectly enrollable batch from checkout.
     * Seat availability (has_seats_available) is checked separately in PHP since
     * it's a derived count, not a column.
     */
    public function scopeSelectableAtCheckout($query)
    {
        return $query->active()->visible()
            ->where(fn ($q) => $q->whereNull('batch_end_date')->orWhere('batch_end_date', '>=', now()->toDateString()));
    }

    /** Number of students currently assigned to this batch (courses + books). */
    public function getEnrolledCountAttribute(): int
    {
        return $this->enrollments()->count() + $this->bookPurchases()->count();
    }

    public function getHasSeatsAvailableAttribute(): bool
    {
        return $this->enrolled_count < $this->student_limit;
    }

    /**
     * Does today's weekday match one of this batch's scheduled class days?
     */
    public function hasClassToday(): bool
    {
        return in_array(now()->format('D'), $this->batch_days ?? [], true);
    }

    /**
     * The next upcoming date (including today) that matches one of batch_days.
     */
    public function nextClassDate(): ?Carbon
    {
        $days = $this->batch_days ?? [];

        if (empty($days)) {
            return null;
        }

        for ($i = 0; $i <= 7; $i++) {
            $candidate = now()->addDays($i);

            if (in_array($candidate->format('D'), $days, true)) {
                return $candidate->startOfDay();
            }
        }

        return null;
    }

    /**
     * The class-link entry (metting_code, full_link, etc.) to show a student today.
     * Prefers a class explicitly scheduled for today; falls back to the most
     * recently added active link so students always have something to copy.
     */
    public function todaysClass(): ?BatchClass
    {
        return $this->classes()
            ->where('status', true)
            ->whereDate('class_start_time', now()->toDateString())
            ->latest('class_start_time')
            ->first()
            ?? $this->classes()->where('status', true)->latest('class_start_time')->first();
    }
}
