<?php

namespace Backpack\Tag\app\Traits;

use Backpack\Tag\app\Models\Tag;

trait Taggable {
  public function tags(){
    return $this->morphToMany(Tag::class, 'taggable', 'ak_taggables')
      ->withPivot('id')
      ->withTimestamps();
  }

  public function scopeWithTags($query)
  {
    return $query->with('tags');
  }

  public function scopeWhereHasTags($query, $tagIds)
  {
    $ids = collect($tagIds)
      ->filter(function ($value) {
        return $value !== null && $value !== '' && is_numeric($value);
      })
      ->map(fn ($value) => (int) $value)
      ->unique()
      ->values();

    if ($ids->isEmpty()) {
      return $query;
    }

    return $query->whereHas('tags', function ($relation) use ($ids) {
      $relation->whereIn('ak_tags.id', $ids);
    });
  }
}
