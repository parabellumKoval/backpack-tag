<?php

namespace Backpack\Reviews\app\Traits;

trait Reviewable {
  public function reviews(){
    $model = config('backpack.reviews.review_model', 'Backpack\Reviews\app\Models\Review');

    return $this->morphMany($model, 'reviewable');
  }

  public function getTotalLickesAttribute() {
    return $this->reviews()->sum('likes');
  }

  public function getTotalDislickesAttribute() {
    return $this->reviews()->sum('dislikes');
  }

  public function getRatingAttribute() {
    $rating = $this->reviews()->avg('rating');
    return $rating? round($rating, 1): null;
  }
  
  public function getDetailedRatingAttribute() {
    return $this->reviews()->avg('extras.rating.avr');
  }

  public function getSingleDetailedRating($key) {
    if(array_key_exists($key, config('backpack.reviews.detailed_rating_params'))) {
      return $this->reviews()->avg('extras.rating.' . $key);
    }else {
      throw new \Exception("Key $key does not exist in allowed array set (config: backpack.reviews.detailed_rating_params). ", 1);
    }
  }
}