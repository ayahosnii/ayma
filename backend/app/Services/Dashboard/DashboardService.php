<?php

namespace App\Services\Dashboard;

use App\Repositories\Dashboard\Contracts\ProductRepositoryInterface;

class DashboardService
{
    protected $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function getDashboardData(): array
    {
        return [
            'totalStats' => $this->productRepository->getTotalStats(),
            'topProducts' => $this->productRepository->getTopSellingProducts(),
            'salesByCountry' => $this->productRepository->getSalesByCountry(),
        ];
    }
}

