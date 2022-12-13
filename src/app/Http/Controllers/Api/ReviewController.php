<?php

namespace Backpack\Reviews\app\Http\Controllers\Api;

use Illuminate\Http\Request;
use Backpack\Reviews\app\Models\Review;

use Backpack\Reviews\app\Http\Resources\ReviewSmallResource;

class ReviewController extends \App\Http\Controllers\Controller
{ 
  public function index(Request $request) {
    $per_page = config('backpack.reviews.per_page');

    $reviews = Review::paginate($per_page);

    return ReviewSmallResource::collection($reviews);
  }
}
