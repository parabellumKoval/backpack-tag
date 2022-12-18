<?php

namespace Backpack\Reviews\app\Http\Controllers\Api;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use Backpack\Reviews\app\Models\Review;

use Backpack\Reviews\app\Http\Resources\ReviewSmallResource;

use \Illuminate\Database\Eloquent\ModelNotFoundException;

class ReviewController extends \App\Http\Controllers\Controller
{ 
  public function index(Request $request) {

    $reviews = Review::query()
              ->select('ak_reviews.*')
              ->distinct('ak_reviews.id')
              ->root()
              ->when(request('reviewable_id'), function($query){
                $query->where('ak_reviews.reviewable_id', request('reviewable_id'));
              })
              ->when(request('reviewable_type'), function($query){
                $query->where('ak_reviews.reviewable_type', request('reviewable_type'));
              });

    $per_page = config('backpack.reviews.per_page');
    $reviews = $reviews->paginate($per_page);

    return ReviewSmallResource::collection($reviews);
  }

  public function create(Request $request) {
    $data = $request->only(['owner', 'text', 'files', 'parent_id', 'reviewable_id', 'reviewable_type']);

    $validator = Validator::make($data, [
      'text' => 'required|string|min:2|max:1000',
      'parent_id' => 'nullable|integer',
      'reviewable_id' => 'nullable|integer',
      'reviewable_type' => 'nullable|string|min:2|max:255',
      'owner.id' => 'nullable|integer',
      'owner.name' => 'nullable|string|min:2|max:100',
      'owner.photo' => 'nullable|string',
      'owner.email' => 'nullable|email',
    ]);

    if ($validator->fails()) {
      return response()->json($validator->errors(), 400);
    }

    $extras = [];

    if(isset($data['owner']) && isset($data['owner']['id'])) {
      $owner_model = config('backpack.reviews.owner_model', 'Backpack\Profile\app\Models\Profile')::find($data['owner']['id']);
    }else if(isset($data['owner'])) {
      
      if(isset($data['owner']['name']))
        $extras['owner']['name'] = $data['owner']['name'];
      
      if(isset($data['owner']['photo']))
        $extras['owner']['photo'] = $data['owner']['photo'];

      if(isset($data['owner']['email']))
        $extras['owner']['email'] = $data['owner']['email'];
      
    }

    $review = Review::create([
      'owner_id' => isset($owner_model)? $owner_model->id: null,
      'text' => $data['text'],
      'extras' => $extras,
      'parent_id' => isset($data['parent_id'])? $data['parent_id']: 0,
      'reviewable_id' => $data['reviewable_id'],
      'reviewable_type' => $data['reviewable_type'],
    ]);

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
