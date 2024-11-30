<?php

namespace App\Repositories\Dashboard;

use App\Models\Product;
use App\Repositories\Dashboard\Contracts\ProductRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ProductRepository implements ProductRepositoryInterface
{
    public function getTopSellingProducts(int $limit = 3): array
    {
        return DB::table('products')
            ->select('name', 'stock')
            ->orderBy('stock', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    public function getSalesByCountry(): array
    {
        $totalSales = $this->getTotalSalesFromOrders();

        return DB::table('orders')
            ->select(
                'shipping_country as country',
                DB::raw('SUM(total_amount) as sales'),
                DB::raw('ROUND(SUM(total_amount) / ? * 100, 2) as percentage', [$totalSales])
            )
            ->where('payment_status', 'paid') // Only consider paid orders
            ->groupBy('shipping_country')
            ->orderByDesc('sales')
            ->get()
            ->toArray();
    }

    public function getTotalStats(): array
    {
        return [
            'total_sales' => $this->getTotalSalesFromOrders(),
            'total_profit' => DB::table('products')->sum(DB::raw('revenue - cost')),
            'orders' => DB::table('orders')->count(),
            'new_customers' => 350 // Example data; replace with actual calculation.
        ];
    }

    private function getTotalSalesFromOrders(): float
    {
        return DB::table('orders')
            ->where('payment_status', 'paid') // Consider only paid orders
            ->sum('total_amount');
    }
}
