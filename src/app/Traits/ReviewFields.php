<?php

namespace Backpack\Reviews\app\Traits;

trait ReviewFields {
  protected function setupReviewFields() {
    $this->crud->addField([
      'name' => 'reviews_amount',
      'label' => 'Кол-во отзывов',
      'value' => $this->crud->getEntry(\Route::current()->parameter('id'))->reviews->count(),
      'tab' => 'Отзывы'
    ]);

    $this->crud->addField([
      'name' => 'reviews',
      'type' => 'relationship',
      'label' => "Отзывы",
      'tab' => 'Отзывы',
    ]);
  }
}