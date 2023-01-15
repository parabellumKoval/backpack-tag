<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use Backpack\Reviews\app\Http\Controllers\Api\ReviewController;

//$auth_guard = config('backpack.reviews.auth_guard', 'profile');

Route::prefix('api/reviews')->controller(ReviewController::class)->group(function () {
  
  Route::get('', 'index');

  Route::get('/{id}', 'show');

  Route::post('', 'create')->middleware('api');
  Route::post('{id}/like', 'likeOrDislike')->middleware('api');

});
