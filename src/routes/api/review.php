<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use Backpack\Reviews\app\Http\Controllers\Api\ReviewController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::prefix('api/reviews')->controller(ReviewController::class)->group(function () {
  
  Route::get('', 'index');

  Route::get('/{id}', 'show');

  Route::post('', 'create');
  Route::post('{id}/like', 'likeOrDislike');

});
