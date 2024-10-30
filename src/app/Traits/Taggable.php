<?php

namespace Backpack\Tag\app\Traits;

trait Taggable {
  public function tags(){
    return $this->morphMany('Backpack\Tag\app\Models\Tag', 'taggable');
  }
}