<?php

namespace Backpack\Reviews\app\Http\Controllers\Api;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

use Backpack\Reviews\app\Models\Review;

use Backpack\Reviews\app\Http\Resources\ReviewSmallResource;

use \Illuminate\Database\Eloquent\ModelNotFoundException;


class ReviewController extends \App\Http\Controllers\Controller
{ 
  public function index(Request $request) {
    
    $reviewable_id = request('reviewable_id');

    if(request('reviewable_slug') && request('reviewable_type')) {
      $reviewable = request('reviewable_type')::where('slug', request('reviewable_slug'))->first();
      $reviewable_id = $reviewable? $reviewable->id: null;
    }

    $reviews = Review::query()
              ->select('ak_reviews.*')
              ->distinct('ak_reviews.id')
              ->root()
              ->when($reviewable_id, function($query) use($reviewable_id){
                $query->where('ak_reviews.reviewable_id', $reviewable_id);
              })
              ->when(request('reviewable_type'), function($query){
                $query->where('ak_reviews.reviewable_type', request('reviewable_type'));
              })
              ->orderBy('created_at', 'desc');

    $per_page = request('per_page')? request('per_page'): config('backpack.reviews.per_page', 12);
    $reviews = $reviews->paginate($per_page);

    return ReviewSmallResource::collection($reviews);
  }

  public function create(Request $request) {
    $data = $request->only(['owner', 'text', 'files', 'parent_id', 'reviewable_id', 'reviewable_type', 'rating', 'extras', 'provider']);

    $validator = Validator::make($data, [
      'text' => 'required|string|min:2|max:1000',
      'parent_id' => 'nullable|integer',
      'reviewable_id' => 'nullable|integer',
      'reviewable_type' => 'nullable|string|min:2|max:255',
      'rating' => 'nullable|integer',
      'owner.id' => 'required_if:provider,id|integer',
      'owner.name' => 'required_if:provider,data|string|min:2|max:100',
      'owner.photo' => 'nullable|string',
      'owner.email' => 'nullable|email',
      'provider' => 'required|string|in:id,data,auth',
      'extras' => 'nullable|array'
    ]);

    if ($validator->fails()) {
      return response()->json($validator->errors(), 400);
    }

    // SET EXTRAS FROM REQUEST
    $extras = isset($data['extras'])? $data['extras']: [];

    // INIT OWNER MODEL
    $owner_model = null;

    // OWNER
    // Set owner by id
    if($data['provider'] === 'id' && isset($data['owner']) && isset($data['owner']['id']))
    {
      try{
        $owner_model = config('backpack.reviews.owner_model', 'Backpack\Profile\app\Models\Profile')::findOrFail($data['owner']['id']);
      }catch(ModelNotFoundException $e) {
        return response()->json($e->getMessage(), 404);
      }

      $extras['owner'] = $owner_model;
    }

    // Set owner from auth session
    else if($data['provider'] === 'auth') 
    {
      if(!Auth::guard(config('backpack.reviews.auth_guard', 'profile'))->check()){
        return response()->json('User not authenticated', 401);
      }

      $owner_model = Auth::guard(config('backpack.reviews.auth_guard', 'profile'))->user();
      $extras['owner'] = $owner_model; 
    }

    // Set owner by exterior data
    else if($data['provider'] === 'data' && isset($data['owner']))
    {
      if(isset($data['owner']['name']))
        $extras['owner']['name'] = $data['owner']['name'];
      
      if(isset($data['owner']['photo']))
        $extras['owner']['photo'] = $data['owner']['photo'];

      if(isset($data['owner']['email']))
        $extras['owner']['email'] = $data['owner']['email']; 
    }

    // CREATE REVIEW
    try {
      $review = Review::create([
        'owner_id' => $owner_model? $owner_model->id: null,
        'text' => $data['text'],
        'rating' => $data['rating'],
        'extras' => $extras,
        'parent_id' => isset($data['parent_id'])? $data['parent_id']: 0,
        'reviewable_id' => isset($data['reviewable_id'])? $data['reviewable_id']: null,
        'reviewable_type' => isset($data['reviewable_type'])? $data['reviewable_type']: null,
      ]);
    }catch(\Expression $e) {
      return response()->json($e->getMessage(), 400);
    }

    return response()->json($review);
  }

  public function likeOrDislike(Request $request, $id) {
    $data = $request->only(['owner_id', 'direction', 'type']);

    $validator = Validator::make($data, [
      'owner_id' => 'required|integer',
      'direction' => [ 
        'nullable',
        Rule::in(['minus', 'plus'])
      ],
      'type' => [
        'required',
        Rule::in(['likes', 'dislikes'])
      ]
    ]);

    $dir = isset($data['direction'])? $data['direction']: 'plus';

    if ($validator->fails()) {
      return response()->json($validator->errors(), 400);
    }

    try {
      $owner_model = config('backpack.reviews.owner_model', 'Backpack\Profile\app\Models\Profile')::findOrFail($data['owner_id']);
    }catch(ModelNotFoundException $e) {
      return response()->json($e->getMessage(), 404);
    }

    try {
      $review_model = Review::findOrFail($id);
    }catch(ModelNotFoundException $e) {
      return response()->json($e->getMessage(), 404);
    }
    
    if($dir === 'plus')
      $review_model->{$data['type']} = $review_model->{$data['type']} + 1;
    else
      $review_model->{$data['type']} = $review_model->{$data['type']} > 0? $review_model->{$data['type']} - 1: 0;


    $review_model->save();

    return response()->json([
        $data['type'] => $review_model->{$data['type']}
    ]);
  }

}
