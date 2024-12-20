<?php

namespace Tests\Feature\Dashboard\Services;

use App\Repositories\Dashboard\Contracts\ProductRepositoryInterface;
use App\Services\Dashboard\DashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Mockery;
use Tests\TestCase;

class DashboardServiceTest extends TestCase
{
    protected $productRepositoryMock;
    protected $dashboardService;

    public function setUp(): void
    {
        parent::setUp();

        // Create a mock for the ProductRepositoryInterface
        $this->productRepositoryMock = Mockery::mock(ProductRepositoryInterface::class);

        // Bind the mock to the container
        $this->app->instance(ProductRepositoryInterface::class, $this->productRepositoryMock);

        // Create an instance of the DashboardService with the mocked repository
        $this->dashboardService = new DashboardService($this->productRepositoryMock);
    }

    /** @test */
    public function it_returns_dashboard_data()
    {
        // Define mock return values for the repository methods
        $this->productRepositoryMock
            ->shouldReceive('getTotalStats')
            ->once()
            ->andReturn([
                'total_sales' => ['value' => 1000, 'change' => 5],
                'total_profit' => ['value' => 500, 'change' => 10],
                'orders' => ['value' => 50, 'change' => 2],
                'new_customers' => ['value' => 10, 'change' => 3],
            ]);

        $this->productRepositoryMock
            ->shouldReceive('getTopSellingProducts')
            ->once()
            ->andReturn([
                ['id' => 1, 'name' => 'Product A', 'units_sold' => 100, 'revenue' => 500, 'stock' => 20],
                ['id' => 2, 'name' => 'Product B', 'units_sold' => 90, 'revenue' => 450, 'stock' => 15],
            ]);

        $this->productRepositoryMock
            ->shouldReceive('getSalesByCountry')
            ->once()
            ->andReturn([
                ['shipping_country' => 'US', 'sales' => 2000, 'percentage' => 50],
                ['shipping_country' => 'CA', 'sales' => 1500, 'percentage' => 37.5],
            ]);

        // Call the method
        $dashboardData = $this->dashboardService->getDashboardData();

        // Assert that the returned data is correct
        $this->assertArrayHasKey('totalStats', $dashboardData);
        $this->assertArrayHasKey('topProducts', $dashboardData);
        $this->assertArrayHasKey('salesByCountry', $dashboardData);

        // Assert total stats
        $this->assertEquals(1000, $dashboardData['totalStats']['total_sales']['value']);
        $this->assertEquals(5, $dashboardData['totalStats']['total_sales']['change']);

        // Assert top products
        $this->assertEquals('Product A', $dashboardData['topProducts'][0]['name']);
        $this->assertEquals(100, $dashboardData['topProducts'][0]['units_sold']);

        // Assert sales by country
        $this->assertEquals('US', $dashboardData['salesByCountry'][0]['shipping_country']);
        $this->assertEquals(2000, $dashboardData['salesByCountry'][0]['sales']);
        $this->assertEquals(50, $dashboardData['salesByCountry'][0]['percentage']);
    }

    public function testGetTotalStats()
    {
        // Mock the methods used in the getTotalStats method
        $mockService = $this->getMockBuilder(DashboardService::class)
            ->onlyMethods(['getTotalSalesFromOrders', 'getNewCustomers', 'calculateChange'])
            ->getMock();

        // Mock the values returned by the methods
        $mockService->method('getTotalSalesFromOrders')->willReturn(1000);
        $mockService->method('getNewCustomers')->willReturn(50);
        $mockService->method('calculateChange')->willReturn(10);

        // Mock database queries
        DB::shouldReceive('table')
            ->with('products')
            ->andReturnSelf();
        DB::shouldReceive('sum')->with(DB::raw('price'))->andReturn(500);

        DB::shouldReceive('table')
            ->with('orders')
            ->andReturnSelf();
        DB::shouldReceive('count')->andReturn(200);

        DB::shouldReceive('table')
            ->with('products')
            ->andReturnSelf();
        DB::shouldReceive('where')->andReturnSelf();
        DB::shouldReceive('sum')->with(DB::raw('price'))->andReturn(400);

        DB::shouldReceive('table')
            ->with('orders')
            ->andReturnSelf();
        DB::shouldReceive('where')->andReturnSelf();
        DB::shouldReceive('count')->andReturn(180);

        // Call the method you want to test
        $result = $mockService->getTotalStats();

        // Assertions to verify the returned values
        $this->assertEquals(1000, $result['total_sales']['value']);
        $this->assertEquals(10, $result['total_sales']['change']);
        $this->assertEquals(500, $result['total_profit']['value']);
        $this->assertEquals(10, $result['total_profit']['change']);
        $this->assertEquals(200, $result['orders']['value']);
        $this->assertEquals(10, $result['orders']['change']);
        $this->assertEquals(50, $result['new_customers']['value']);
        $this->assertEquals(10, $result['new_customers']['change']);
    }

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
