<?php

namespace Backpack\Tag\app\Traits;

use Backpack\Tag\app\Models\Tag;

trait Taggable {
  public function tags(){
    return $this->morphToMany(Tag::class, 'taggable', 'ak_taggables')->withPivot('id');
  }
}