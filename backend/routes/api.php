<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ColorController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\GrammarOptionController;
use App\Http\Controllers\Api\GrammarQuestionController;
use App\Http\Controllers\Api\LevelsController;
use App\Http\Controllers\Api\OpenAIController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\OrderItemController;
use App\Http\Controllers\Api\RefundController;
use App\Http\Controllers\Api\SpeakingController;
use App\Http\Controllers\Api\StoriesController;
use App\Http\Controllers\Api\SizeController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\GrammarGamesController;
use App\Http\Controllers\Api\ListeningController;
use App\Http\Controllers\Api\ListeningQuestionController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\MongoControllers\ProductsMongoController;
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
    Route::get('/dashboard', [DashboardController::class, 'index']);

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

    Route::resource('/orders', OrderController::class); //Orders
    Route::resource('/order_items', OrderItemController::class); //Order items
    Route::get('/count-orders', [OrderController::class, 'countOrders']); //Order Count


    Route::post('refund', [RefundController::class, 'processRefund']); //Levels

    Route::resource('/levels', LevelsController::class);
    Route::get('/count-levels', [LevelsController::class, 'countLevels']); //Levels Count
    Route::get('getAllLevels', [LevelsController::class, 'getAllLevels']); //Levels

    Route::resource('/grammar-games', GrammarGamesController::class); // Grammar Games

    Route::get('/customers', [CustomerController::class, 'index']); //Stories Count

    Route::resource('/stories', StoriesController::class);
    Route::get('/count-stories', [StoriesController::class, 'count']); //Stories Count
    Route::get('/stories/{id}/sounds', [StoriesController::class, 'getStorySounds']);     // Sounds table for each story
    Route::delete('stories/{storyId}/sounds/{soundId}', [StoriesController::class, 'destroySound']); // Delete Sound
    Route::post('stories/{story}/sounds', [StoriesController::class, 'addSound']); // Add Sound
    Route::get('/stories/{id}/slides', [StoriesController::class, 'getStorySlides']); // Slides
    Route::post('stories/{storyId}/slides', [StoriesController::class, 'addSlide']); // Add Slide



});
Route::post('/ratings', [RatingController::class, 'store']);
