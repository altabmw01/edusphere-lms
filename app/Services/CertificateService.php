<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class CertificateService
{
    /**
     * Issue a certificate for a completed course, generating a downloadable PDF.
     * Idempotent: returns the existing certificate if one was already issued.
     */
    public function issue(User $user, Course $course): Certificate
    {
        $existing = Certificate::where('user_id', $user->id)->where('course_id', $course->id)->first();

        if ($existing) {
            return $existing;
        }

        $certificate = Certificate::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'issued_at' => now(),
        ]);

        $pdf = Pdf::loadView('certificates.template', [
            'certificate' => $certificate,
            'user' => $user,
            'course' => $course,
        ])->setPaper('a4', 'landscape');

        $path = "certificates/{$certificate->certificate_number}.pdf";
        Storage::disk('public')->put($path, $pdf->output());

        $certificate->update(['file_path' => $path]);

        return $certificate;
    }

    public function isEligible(User $user, Course $course): bool
    {
        $enrollment = $user->enrollments()->where('course_id', $course->id)->first();

        return $enrollment && $enrollment->progress_percent >= 100;
    }
}
