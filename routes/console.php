<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduled Tasks
|--------------------------------------------------------------------------
*/

// Deactivate expired coupons daily so checkout validation stays accurate.
Schedule::command('lms:deactivate-expired-coupons')->dailyAt('00:05');

// Clean up abandoned guest cart items older than 30 days.
Schedule::command('lms:prune-stale-carts')->daily();

// Send a weekly digest email to teachers summarizing new enrollments and revenue.
Schedule::command('lms:teacher-weekly-digest')->weeklyOn(1, '08:00');
