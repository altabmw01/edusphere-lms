<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function index(): View
    {
        return view('admin.activity-logs.index', [
            'logs' => ActivityLog::with('user')->latest()->paginate(30),
        ]);
    }
}
