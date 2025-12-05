<?php

namespace Backpack\Tag\app\Traits;

use Backpack\Tag\app\Models\Tag;
use Illuminate\Database\Eloquent\Model as EloquentModel;

trait TagFields {

  protected function setupFilers() {

    $this->crud->addFilter([
      'name' => 'tags',
      'label' => 'Теги',
      'type' => 'select2',
    ], function(){
      $tags = Tag::query()->orderBy('value')->pluck('label', 'id')->toArray();
      $tags['no'] = 'Без тега';

      return $tags;
    }, function($id){
      if($id === 'no') {
        $this->crud->query->has('tags', '=', 0);
      }else {
        $this->crud->query->whereHas('tags', function ($query) use ($id) {
          $query->where('ak_tags.id', $id);
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
      'ajax' => false,
    ]);
  }


  protected function setupTagColumns() {

    $this->crud->addColumn([
      'name' => 'tags',
      'type' => 'view',
      'view' => 'tag::columns.tags_badges',
      'data-type' => $this->resolveTaggableMorphClass(),
      'label' => "Теги",
      'priority' => 1
    ]);
  }

  protected function resolveTaggableMorphClass(): ?string
  {
    if (method_exists($this->crud, 'getModel')) {
      $model = $this->crud->getModel();

      if ($model instanceof EloquentModel) {
        return $model->getMorphClass();
      }

      if (is_string($model) && class_exists($model)) {
        return (new $model())->getMorphClass();
      }
    }

    if (property_exists($this->crud, 'model') && $this->crud->model instanceof EloquentModel) {
      return $this->crud->model->getMorphClass();
    }

    if (property_exists($this->crud, 'model') && is_string($this->crud->model) && class_exists($this->crud->model)) {
      return (new $this->crud->model())->getMorphClass();
    }

    return null;
  }
}
