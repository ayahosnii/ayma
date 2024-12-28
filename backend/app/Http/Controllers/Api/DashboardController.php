<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Dashboard\DashboardService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index()
    {
        $dashboardData = $this->dashboardService->getDashboardData();

        return response()->json([
            'status' => 'success',
            'data' => $dashboardData,
        ]);
    }


    public function getOrdersData()
    {
        // Get the current year
        $currentYear = Carbon::now()->year;

        // Query the orders from the database, grouped by month
        $ordersData = Order::whereYear('created_at', $currentYear)
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as order_count')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Create an array of months with default order_count = 0
        $months = collect(range(1, 12))->map(function ($month) use ($currentYear, $ordersData) {
            // Check if the current month exists in the ordersData
            $order = $ordersData->firstWhere('month', $month);
            return [
                Carbon::createFromDate($currentYear, $month, 1)->timestamp * 1000, // Unix timestamp in milliseconds
                $order ? $order->order_count : 0,
            ];
        });

        // Find the month with the most orders
        $mostOrdersMonth = $months->max(function ($data) {
            return $data[1]; // Access the order_count from the tuple
        });

        $mostOrdersMonthDetails = $months->first(function ($data) use ($mostOrdersMonth) {
            return $data[1] === $mostOrdersMonth;
        });

        return response()->json([
            'ordersData' => $months->values(),
            'mostOrdersMonth' => [
                'month' => Carbon::createFromTimestampMs($mostOrdersMonthDetails[0])->format('F'),
                'orders' => $mostOrdersMonth,
            ],
        ]);
    }
}
