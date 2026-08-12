<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Review;
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
        return view('manager.dashboard', [
            'revenueThisMonth' => Order::query()
                ->where('payment_status', 'paid')
                ->whereMonth('paid_at', now()->month)
                ->whereYear('paid_at', now()->year)
                ->sum('grand_total'),
            'pendingOrders' => Order::where('status', 'pending')->count(),
            'processingOrders' => Order::where('status', 'processing')->count(),
            'activeCoupons' => Coupon::where('status', true)->count(),
            'pendingReviews' => Review::pending()->count(),
            'totalStudents' => User::role(User::ROLE_STUDENT)->count(),
            'recentOrders' => Order::with('user')->latest()->limit(8)->get(),
            'pendingReviewsList' => Review::pending()->with(['user', 'reviewable'])->latest()->limit(6)->get(),
            'monthlySales' => $this->reports->monthlySales(6),
        ]);
    }
}
