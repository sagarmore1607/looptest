<?php

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::get('/orders','OrdersController@index');
Route::post('/orders','OrdersController@store');
Route::put('/orders/{order_id}','OrdersController@update');
Route::get('/orders/{order_id}','OrdersController@show');
Route::delete('/orders/{order_id}','OrdersController@destroy');

Route::post('/orders/{order_id}/add','OrdersController@addProductToOrder');
Route::post('/orders/{order_id}/pay','OrdersController@orderPayment');
