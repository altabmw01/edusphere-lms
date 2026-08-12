<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Exports\OrdersExport;
use App\Models\Order;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function __construct(protected ReportService $reports)
    {
    }

    public function index(Request $request): View
    {
        $from = $request->date('from') ?? now()->subDays(30);
        $to = $request->date('to') ?? now();

        $orders = Order::paid()->whereBetween('created_at', [$from, $to])->get();

        return view('admin.reports.index', [
            'from' => $from,
            'to' => $to,
            'totalRevenue' => $orders->sum('grand_total'),
            'totalOrders' => $orders->count(),
            'topCourses' => $this->reports->topSellingCourses(10),
            'topBooks' => $this->reports->topSellingBooks(10),
            'monthlySales' => $this->reports->monthlySales(12),
        ]);
    }

    public function exportOrdersExcel(Request $request)
    {
        return Excel::download(new OrdersExport($request->only(['from', 'to'])), 'orders-report.xlsx');
    }

    public function exportOrdersPdf(Request $request)
    {
        $orders = Order::paid()->with('user')->latest()->get();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reports.pdf', ['orders' => $orders]);

        return $pdf->download('orders-report.pdf');
    }
}
