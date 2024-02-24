<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

$review_controller = config('backpack.reviews.review_controller_api', 'Backpack\Reviews\app\Http\Controllers\Api\ReviewController');

Route::prefix('api/review')->controller($review_controller)->group(function () {
  
  Route::get('', 'index');

  Route::get('/{id}', 'show');

  Route::post('', 'create')->middleware('api');
  
  Route::post('{id}/like', 'likeOrDislike')->middleware('api');

});
