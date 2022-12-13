<?php

namespace Aimix\Review\app\Http\Controllers;
use Aimix\Review\app\Http\Requests\ReviewRequest;

use App\Http\Controllers\Controller as BaseController;
use Illuminate\Http\Request;
use Aimix\Review\app\Models\Review;
use Aimix\Shop\app\Models\Product;
use Backpack\PageManager\app\Models\Page;

class ReviewController extends BaseController
{
  public function index(Request $request , $type = null)
  {
    $reviews = Review::where('is_moderated', 1)->where('parent_id', 0)->where('product_id', null)->orderBy('created_at', 'desc')->paginate(config('aimix.review.per_page'));

	$page = Page::find(9)->withFakes();
	
	if($request->isJson)
		return response()->json(['reviews' => $reviews->withPath($request->url().'&page='.$request->page)]);
	else
		return view('reviews.index')->with('reviews', $reviews)->with('type', $type)->with('page', $page);	
/*
    if($request->isJson)
      return response()->json(['reviews' => $reviews->withPath($request->url().'&page='.$request->page)]);
    else
      return view('reviews.index')->with('reviews', $reviews)->with('type', $type)->with('page', $page);
*/
  }
  
  public function requestSearchList(Request $request, $value) {
      $values = [];
      
      if($value) {
        $values = Product::where('is_active', 1)->where('name', 'like', '%'.$value.'%')->get();
      }
      
      return response()->json($values);
    }
  
  // public function create(ReviewRequest $request, $type = 'text') 
  // {
    
  //   $review = new Review;
  //   $review->type = $type;
  //   $review->name = $request->input($type . '_review_name');
  //   $review->email = $request->input($type . '_review_email');
  //   $review->text = $request->input($type . '_review_text');
    
  //   if(config('aimix.review.enable_review_for_product'))
  //     $review->product_id = $request->input($type . '_review_product_id');
      
  //   if(config('aimix.review.enable_rating'))
  //     $review->rating = $request->input($type . '_review_rating');
    
      
  //   if($request->file($type . '_review_file')) {
  //     $path = $request->file($type . '_review_file')->store('reviews', 'reviews');
      
  //     $review->file = '/uploads/' . $path;
  //   }

  //   $review->save();

  //   return back()->with('message', __('main.review_success'))->with('type', 'review');
  // }
}