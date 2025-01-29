<?php

namespace Backpack\Tag\app\Traits;

use Backpack\Tag\app\Models\Tag;

trait TagFields {

  protected function setupFilers() {

    $this->crud->addFilter([
      'name' => 'tags',
      'label' => 'Теги',
      'type' => 'select2',
    ], function(){
      $tags = Tag::all()->pluck('text', 'id')->toArray();
      $tags['no'] = 'Без тега';

      return $tags;
    }, function($id){
      if($id === 'no') {
        $this->crud->query->has('tags', '=', 0);
      }else {
        $this->crud->query->whereHas('tags', function ($query) use ($id) {
          $query->where('tag_id', $id);
        });
      }
    });
  }

  protected function setupTagFields() {

    //
    // $this->crud->addField([
    //   'name' => 'reviews_amount',
    //   'label' => 'Кол-во отзывов',
    //   'value' => $this->crud->getEntry(\Route::current()->parameter('id'))->reviews->count(),
    //   'tab' => 'Отзывы'
    // ]);

    $this->crud->addField([
      'name' => 'tags',
      'type' => 'relationship',
      'label' => "Теги",
    ]);
  }


  protected function setupTagColumns() {

    $this->crud->addColumn([
      'name' => 'tags',
      'type' => 'tag',
      'data-type' => 'Backpack\Store\app\Models\Admin\Product',
      'label' => "Теги",
      'priority' => 1
    ]);
  }
}