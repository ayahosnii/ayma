<?php

namespace App\Services\Dashboard;

use App\Repositories\Dashboard\Contracts\ProductRepositoryInterface;
use Illuminate\Support\Facades\Auth;

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
            'userName' => Auth::user()->name,
            'totalStats' => $this->productRepository->getTotalStats(),
            'topProducts' => $this->productRepository->getTopSellingProducts(),
            'salesByCountry' => $this->productRepository->getSalesByCountry(),
            'userData' => $this->productRepository->getUserData(),

        ];
    }
}

