<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    @page { margin: 0; }
    body { font-family: DejaVu Sans, sans-serif; margin: 0; padding: 0; }
    .frame {
        width: 100%; height: 100%; min-height: 720px; box-sizing: border-box;
        border: 14px solid #2563EB; padding: 50px; text-align: center; position: relative;
    }
    .inner-border { border: 1.5px solid #94A3B8; padding: 40px; height: 100%; box-sizing: border-box; }
    .eyebrow { color: #2563EB; letter-spacing: 4px; font-size: 13px; font-weight: bold; text-transform: uppercase; }
    .brand { font-size: 22px; font-weight: bold; color: #1E293B; margin-bottom: 20px; }
    h1 { font-size: 40px; color: #1E293B; margin: 20px 0 10px; }
    .student-name { font-size: 32px; color: #2563EB; font-weight: bold; margin: 20px 0; border-bottom: 2px solid #E2E8F0; display: inline-block; padding-bottom: 10px; }
    .course-title { font-size: 20px; color: #1E293B; margin: 10px 0 30px; font-weight: bold; }
    .desc { color: #64748B; font-size: 13px; max-width: 560px; margin: 0 auto 30px; }
    .footer-row { display: flex; justify-content: space-between; margin-top: 50px; padding: 0 60px; }
    .footer-col { text-align: center; font-size: 12px; color: #64748B; }
    .footer-col strong { display: block; font-size: 14px; color: #1E293B; margin-bottom: 4px; }
    .cert-number { position: absolute; bottom: 20px; right: 40px; font-size: 10px; color: #94A3B8; }
</style>
</head>
<body>
<div class="frame">
    <div class="inner-border">
        <div class="brand">🎓 EduSphere</div>
        <div class="eyebrow">Certificate of Completion</div>
        <h1>This certifies that</h1>
        <div class="student-name">{{ $certificate->user->name }}</div>
        <p class="desc">has successfully completed the course</p>
        <div class="course-title">{{ $certificate->course->title }}</div>
        <p class="desc">demonstrating dedication and mastery of the skills covered throughout the {{ duration_for_humans($certificate->course->duration_minutes) }} program.</p>

        <div class="footer-row">
            <div class="footer-col">
                <strong>{{ $certificate->issued_at->format('F d, Y') }}</strong>
                Date Issued
            </div>
            <div class="footer-col">
                <strong>{{ $certificate->course->teacher?->name }}</strong>
                Course Instructor
            </div>
        </div>

        <div class="cert-number">Certificate No. {{ $certificate->certificate_number }} &middot; Verify at edusphere.test/verify</div>
    </div>
</div>
</body>
</html>
