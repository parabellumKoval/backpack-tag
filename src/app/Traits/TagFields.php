<?php

namespace Backpack\Tag\app\Traits;

trait TagFields {
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