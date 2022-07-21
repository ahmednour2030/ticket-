<?php

use App\Http\Controllers\Api\InvoiceController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    Route::post('invoice', [InvoiceController::class, 'store']);
    Route::post('invoice/entry', [InvoiceController::class, 'store']);
});

Route::group([ 'middleware' => 'api'], function (){
    Route::post('invoice', [InvoiceController::class, 'store']);
    Route::post('invoice/entry', [InvoiceController::class, 'entry']);
});

Route::get('test', function (){
   return 'test api';
});
