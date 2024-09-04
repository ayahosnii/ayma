<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ColorController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\GrammarOptionController;
use App\Http\Controllers\Api\GrammarQuestionController;
use App\Http\Controllers\Api\LevelsController;
use App\Http\Controllers\Api\OpenAIController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\OrderItemController;
use App\Http\Controllers\Api\SpeakingController;
use App\Http\Controllers\Api\StoriesController;
use App\Http\Controllers\Api\SizeController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\GrammarGamesController;
use App\Http\Controllers\Api\ListeningController;
use App\Http\Controllers\Api\ListeningQuestionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

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
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:api')->group(function () {
    Route::resource('/categories', CategoryController::class);
    Route::get('/count-categories', [CategoryController::class, 'count']); //Categories Count

    Route::resource('/products', ProductController::class);
    Route::get('/count-products', [ProductController::class, 'countProducts']); //Product Count

    Route::resource('/colors', ColorController::class); //Products Colors
    Route::resource('/sizes', SizeController::class); //Products Sizes

    Route::resource('/orders', OrderController::class); //Orders
    Route::resource('/order_items', OrderItemController::class); //Order items


    Route::resource('/levels', LevelsController::class);
    Route::get('/count-levels', [LevelsController::class, 'countLevels']); //Levels Count
    Route::get('getAllLevels', [LevelsController::class, 'getAllLevels']); //Levels

    Route::resource('/grammar-games', GrammarGamesController::class); // Grammar Games


    Route::get('/customers', [CustomerController::class, 'index']); //Stories Count
});
