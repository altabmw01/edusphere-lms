<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CertificateController extends Controller
{
    public function index(): View
    {
        $certificates = Certificate::where('user_id', Auth::id())
            ->with('course')
            ->latest()
            ->paginate(9);

        return view('student.certificates.index', ['certificates' => $certificates]);
    }

    public function download(Certificate $certificate)
    {
        abort_unless($certificate->user_id === Auth::id(), 403);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('certificates.template', [
            'certificate' => $certificate->load('course', 'user'),
        ])->setPaper('a4', 'landscape');

        return $pdf->download("certificate-{$certificate->certificate_number}.pdf");
    }
}
