<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ColorController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\InventoryController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\OrderItemController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\RefundController;
use App\Http\Controllers\Api\RolesController;
use App\Http\Controllers\Api\ShippingCompanyController;
use App\Http\Controllers\Api\SizeController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\MongoControllers\ProductsMongoController;
use App\Http\Controllers\SupplierController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/get-products', [ProductController::class, "getProducts"]);


Route::group(['namespace' => 'Api', 'prefix' => 'v1'], function () {
    Route::post('/login', [AuthController::class, 'login']);
});
Route::post('/logout', function (Request $request) {
    // Revoke the authenticated user's token
    $request->user()->token()->revoke();

    return response()->json(['message' => 'Logged out successfully'], 200);
})->middleware('auth:api');

Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:api')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/order-statistics', [DashboardController::class, 'getOrdersData']);
    //Route::get('/profile', [ProfileController::class, 'getProfile']);
    Route::resource('/profile', ProfileController::class);


    /******************************************* Start Categories *******************************************/
    Route::resource('/categories', CategoryController::class);
    Route::get('/categories/parents', [CategoryController::class, 'getParentCategories']); // Get parent categories
    Route::get('/categories/{parentId}/children', [CategoryController::class, 'getChildCategories']); // Get child categories for a specific parent
    Route::get('/count-categories', [CategoryController::class, 'count']);
    /******************************************* End  Categories *******************************************/
    /******************************************* Start Products *******************************************/
    Route::resource('/products', ProductController::class);
    Route::get('/count-products', [ProductController::class, 'countProducts']); //Product Count
    Route::put('products/{product}/stock', [ProductController::class, 'updateStock']);
    Route::resource('/products-mongo', ProductsMongoController::class);
    Route::post('/products-mongo/update/{products_mongo}', [ProductsMongoController::class, 'update']);
    Route::get('/count-products-mongo', [ProductsMongoController::class, 'countProductsMongo']); //Product Count
    /******************************************* End   Products *******************************************/

    Route::resource('/colors', ColorController::class); //Products Colors
    Route::resource('/sizes', SizeController::class); //Products Sizes
    /******************************************* Start Suppliers *******************************************/
    Route::get('/suppliers', [SupplierController::class, 'index']);
    Route::post('/suppliers', [SupplierController::class, 'store']);
    Route::get('/suppliers/{id}', [SupplierController::class, 'show']);
    Route::put('/suppliers/{id}', [SupplierController::class, 'update']);
    Route::delete('/suppliers/{id}', [SupplierController::class, 'destroy']);
    Route::get('/count-suppliers', [SupplierController::class, 'countSuppliers']);
    /******************************************* End   Suppliers *******************************************/
    /******************************************* Start Suppliers *******************************************/
    Route::get('/roles', [RolesController::class, 'index']);
    Route::get('/users', [UserController::class, 'index']);
    Route::post('users', [UserController::class, 'store']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
    Route::get('/count-users', [UserController::class, 'countUsers']);
    /******************************************* End   Suppliers *******************************************/

    Route::resource('/orders', OrderController::class); //Orders
    Route::resource('/order_items', OrderItemController::class); //Order items
    Route::get('/count-orders', [OrderController::class, 'countOrders']); //Order Count
    Route::resource('/deliveries', DeliveryController::class); //Deliveries
    Route::get('/count-deliveries', [DeliveryController::class, 'countDeliveries']); //Deliveries count
    Route::put('/orders/{id}/status', [OrderController::class, 'updateStatus']);

    /******************************************* Start Inventory *******************************************/
    Route::prefix('inventory')->group(function () {
        Route::get('/', [InventoryController::class, 'index']); // Fetch inventory updates (paginated)
        Route::post('/', [InventoryController::class, 'store']); // Add a new inventory update
        Route::get('/{id}', [InventoryController::class, 'show']); // Get details of a specific inventory update
        Route::put('/{id}', [InventoryController::class, 'update']); // Update an inventory update
        Route::delete('/{id}', [InventoryController::class, 'destroy']); // Delete an inventory update

    });
        Route::get('/count-inventoryrecords', [InventoryController::class, 'countInvetoryRecords']); //Inventory Records Count
    /******************************************* End   Inventory *******************************************/
    /******************************************* Start Shipping Company *******************************************/
    Route::resource('shipping-companies', ShippingCompanyController::class);
    /******************************************* End   Shipping Company *******************************************/
    Route::post('refund', [RefundController::class, 'processRefund']); //Levels

    Route::get('/customers', [CustomerController::class, 'index']); //Stories Count


});
Route::post('/ratings', [RatingController::class, 'store']);
