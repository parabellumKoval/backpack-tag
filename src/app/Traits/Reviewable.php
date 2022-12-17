<?php

namespace Backpack\Reviews\app\Traits;

trait Reviewable {
  public function reviews(){
    return $this->morphMany('Backpack\Reviews\app\Models\Review', 'reviewable');
  }
}