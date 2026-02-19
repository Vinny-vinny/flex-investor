<?php

use App\Http\Controllers\PaymentsController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;
use SmoDav\Mpesa\Laravel\Facades\STK;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/stk-push', function () {
    $response = STK::request(5)
        ->from('254704522671')
        ->usingReference('234233555', 'stk push')
        ->push();
});

Route::group(["prefix" => "v1"], function () {
    Route::get("products", [ProductsController::class, "index"]);
    Route::post("join", [ProductsController::class, "join"]);
    Route::get("user-by-phone/{phone}", [UsersController::class, "getUserByPhone"]);
    Route::post("register", [UsersController::class, "onboardUser"]);
    Route::post("promoter-signup", [UsersController::class, "promoterSignup"]);
    Route::post("handle-stk-callback", [PaymentsController::class, "handleStkCallback"]);
    Route::post("handle-c2b-callback", [PaymentsController::class, "handleC2bCallback"]);
    Route::post("save", [ProductsController::class, "save"]);
    Route::get("my-chamas/{phoneNumber}", [ProductsController::class, "getUserChamas"]);
});
