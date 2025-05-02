<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('/operational')->middleware(['accessapioperational','logrequest'])->group(function () {
    Route::post('login', 'Api\App\Operational\LoginController@login')
    ->name('login');
    Route::post('test', 'Api\App\Operational\LoginController@test')
    ->name('test');
    
    Route::prefix('/calculate-payslip')->middleware(['logrequest'])->group(function () {
        Route::post('/', 'Api\App\Operational\CalculatePayslipController@calculate')->name('calculatepayslip');
    });
});


Route::prefix('/xndcallback')->middleware(['logrequest','aksesxenditcallback'])->group(function () {
    Route::post('/', 'Api\XenditCallback\XenditCallbackController@callback')->name('callback');
});

Route::prefix('/paperidcallback')->middleware('logrequest')->group(function () {
    Route::post('/', 'Api\PaperidCallback\PaperidCallbackController@callback')->name('paperidcallback');
});