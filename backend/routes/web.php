<?php

use App\Events\MyEvent;
use App\Models\Order;
use BeyondCode\LaravelWebSockets\Facades\WebSocketsRouter;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/trigger', function () {
    $message = "Hello, this is a test broadcast!";
    event(new MyEvent($message));
    return "Event has been sent!";
});

Route::get('/test-broadcast', function () {
    $order = Order::first();
    broadcast(new \App\Events\OrderCreated($order));
    return 'Broadcast sent!';
});

