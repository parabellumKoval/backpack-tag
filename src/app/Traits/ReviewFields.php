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
      'label' => "Tags",
    ]);
  }
}