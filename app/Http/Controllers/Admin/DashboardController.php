<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Course;
use App\Models\Order;
use App\Models\User;
use App\Services\ReportService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(protected ReportService $reports)
    {
    }

    public function index(): View
    {
        return view('admin.dashboard', [
            'stats' => $this->reports->dashboardStats(),
            'monthlySales' => $this->reports->monthlySales(),
            'recentOrders' => Order::with('user')->latest()->limit(8)->get(),
            'latestUsers' => User::latest()->limit(6)->get(),
            'latestCourses' => Course::with('teacher')->latest()->limit(5)->get(),
            'latestBooks' => Book::latest()->limit(5)->get(),
            'topCourses' => $this->reports->topSellingCourses(),
            'topBooks' => $this->reports->topSellingBooks(),
        ]);
    }
}
