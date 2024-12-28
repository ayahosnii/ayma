<?php

namespace App\Repositories\Dashboard;

use App\Http\Resources\SalesMetricResource;
use App\Models\Product;
use App\Models\SalesMetric;
use App\Models\User;
use App\Repositories\Dashboard\Contracts\ProductRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProductRepository implements ProductRepositoryInterface
{
    public function getTopSellingProducts(int $limit = 3)
    {
        $salesMetrics = SalesMetric::with(['product.primaryImage'])
            ->orderByDesc('sales_metrics.units_sold')
            ->limit($limit)
            ->get();

        // Return the resources wrapped in the response
        return SalesMetricResource::collection($salesMetrics);
    }

    public function getSalesByCountry(int $limit = 3): array
    {
        $totalSales = $this->getTotalSalesFromOrders();

        return DB::table('orders')
            ->select(
                'shipping_country',
                DB::raw('SUM(total_amount) as sales'),
                DB::raw('ROUND(SUM(total_amount) / ? * 100, 2) as percentage')
            )
            ->addBinding($totalSales, 'select') // Add the binding for the placeholder
            ->where('payment_status', 'paid') // Only consider paid orders
            ->groupBy('shipping_country')
            ->orderByDesc('sales')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    public function getTotalStats(): array
    {
        // Get the total sales, profit, orders, and new customers
        $totalSales = $this->getTotalSalesFromOrders();
        $totalProfit = DB::table('products')->sum(DB::raw('price'));
        $orderCount = DB::table('orders')->count();
        $newCustomerCount = $this->getNewCustomers();

        // Calculate the change for each metric (compared to last year, or last month, etc.)
        $lastYearSales = $this->getTotalSalesFromOrders(Carbon::now()->subYear()); // Sales from last year
        $lastYearProfit = DB::table('products')
            ->where('created_at', '>=', Carbon::now()->subYear())
            ->sum(DB::raw('price'));
        $lastMonthOrders = DB::table('orders')
            ->where('created_at', '>=', Carbon::now()->subMonth())
            ->count();
        $lastMonthNewCustomers = $this->getNewCustomers(30); // New customers in the last month

        return [
            'total_sales' => [
                'value' => $totalSales,
                'change' => $this->calculateChange($totalSales, $lastYearSales)
            ],
            'total_profit' => [
                'value' => $totalProfit,
                'change' => $this->calculateChange($totalProfit, $lastYearProfit)
            ],
            'orders' => [
                'value' => $orderCount,
                'change' => $this->calculateChange($orderCount, $lastMonthOrders)
            ],
            'new_customers' => [
                'value' => $newCustomerCount,
                'change' => $this->calculateChange($newCustomerCount, $lastMonthNewCustomers)
            ]
        ];
    }

    private function calculateChange($current, $previous): float
    {
        // Avoid division by zero
        if ($previous == 0) {
            return 0;
        }

        return (($current - $previous) / $previous) * 100;
    }

    private function getTotalSalesFromOrders(): float
    {
        return DB::table('orders')
            ->where('payment_status', 'paid')
            ->sum('total_amount');
    }

    public function getNewCustomers(int $days = 30)
    {
        // Get new customers who were created in the last $days days
        return User::where('created_at', '>=', Carbon::now()->subDays($days))
            ->count();
    }

    public function getUserData(int $limit = 10)
    {
        return User::with('roles') // Eager load the roles relationship
        ->select('name') // Select the necessary columns
        ->orderBy('created_at', 'desc') // Order by creation date
        ->limit($limit) // Limit the number of users returned
        ->get()
            ->map(function ($user) {
                // Include the role names in the result
                $user->roles = $user->roles->pluck('name'); // Get all roles assigned to the user
                return $user;
            });
    }
}
