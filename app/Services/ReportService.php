<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Course;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function dashboardStats(): array
    {
        return [
            'revenue' => Order::paid()->sum('grand_total'),
            'orders' => Order::count(),
            'books_sold' => Order::paid()->join('order_items', 'orders.id', '=', 'order_items.order_id')
                ->where('order_items.purchasable_type', Book::class)->sum('order_items.quantity'),
            'courses_sold' => Order::paid()->join('order_items', 'orders.id', '=', 'order_items.order_id')
                ->where('order_items.purchasable_type', Course::class)->sum('order_items.quantity'),
            'students' => User::role('student')->count(),
            'teachers' => User::role('teacher')->count(),
            'managers' => User::role('manager')->count(),
        ];
    }

    public function monthlySales(int $months = 6): array
    {
        $from = Carbon::now()->subMonths($months - 1)->startOfMonth();

        $rows = Order::paid()
            ->where('created_at', '>=', $from)
            ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"), DB::raw('SUM(grand_total) as total'))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $labels = [];
        $data = [];
        for ($i = 0; $i < $months; $i++) {
            $key = $from->copy()->addMonths($i)->format('Y-m');
            $labels[] = $from->copy()->addMonths($i)->format('M Y');
            $data[] = (float) ($rows[$key] ?? 0);
        }

        return ['labels' => $labels, 'data' => $data];
    }

    public function topSellingCourses(int $limit = 5)
    {
        return Course::published()->orderByDesc('sales_count')->limit($limit)->get(['id', 'title', 'sales_count', 'price']);
    }

    public function topSellingBooks(int $limit = 5)
    {
        return Book::published()->orderByDesc('sales_count')->limit($limit)->get(['id', 'title', 'sales_count', 'price']);
    }

    public function teacherStats(int $teacherId): array
    {
        $courseIds = Course::where('teacher_id', $teacherId)->pluck('id');

        return [
            'total_courses' => $courseIds->count(),
            'total_students' => \App\Models\CourseEnrollment::whereIn('course_id', $courseIds)->distinct('user_id')->count('user_id'),
            'revenue' => \App\Models\OrderItem::where('purchasable_type', Course::class)
                ->whereIn('purchasable_id', $courseIds)
                ->whereHas('order', fn ($q) => $q->paid())
                ->sum('line_total'),
            'pending_reviews' => \App\Models\Review::whereIn('reviewable_id', $courseIds)
                ->where('reviewable_type', Course::class)
                ->pending()
                ->count(),
        ];
    }
}
