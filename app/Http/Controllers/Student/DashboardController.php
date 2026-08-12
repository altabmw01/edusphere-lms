<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Order;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        return view('student.dashboard', [
            'enrollments' => $user->enrollments()->with('course')->latest()->limit(4)->get(),
            'bookPurchases' => $user->bookPurchases()->with('book')->latest()->limit(4)->get(),
            'wishlistCount' => Wishlist::where('user_id', $user->id)->count(),
            'ordersCount' => Order::where('user_id', $user->id)->count(),
            'certificatesCount' => Certificate::where('user_id', $user->id)->count(),
            'coursesInProgress' => $user->enrollments()->where('progress_percent', '<', 100)->count(),
            'coursesCompleted' => $user->enrollments()->where('progress_percent', '>=', 100)->count(),
            'recentOrders' => Order::where('user_id', $user->id)->latest()->limit(5)->get(),
        ]);
    }
}
