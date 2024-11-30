<?php

namespace App\Repositories\Dashboard;

use App\Models\Product;
use App\Models\SalesMetric;
use App\Models\User;
use App\Repositories\Dashboard\Contracts\ProductRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProductRepository implements ProductRepositoryInterface
{
    public function getTopSellingProducts(int $limit = 10)
    {
        // Query the sales_metrics table to get the top-selling products
        return SalesMetric::join('products', 'sales_metrics.product_id', '=', 'products.id')
            ->select(
                'products.id',
                'products.name',
                'sales_metrics.units_sold',
                'sales_metrics.revenue',
                'products.stock' // Add stock_quantity (or the relevant column for stock)
            )
            ->orderByDesc('sales_metrics.units_sold') // Order by units sold, or use revenue if preferred
            ->limit($limit)
            ->get();
    }

    public function getSalesByCountry(): array
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
}
