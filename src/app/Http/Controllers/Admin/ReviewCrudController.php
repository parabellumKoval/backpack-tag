<?php

namespace Backpack\Reviews\app\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

use Backpack\Reviews\app\Http\Requests\ReviewRequest;
use Backpack\Reviews\app\Models\Review;

/**
 * Class ReviewCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class ReviewCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    //use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup()
    {
        $this->crud->setModel('Backpack\Reviews\app\Models\Review');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/review');
        $this->crud->setEntityNameStrings('отзыв', 'отзывы');
    }

    protected function setupShowOperation()
    {
	      //$this->crud->set('show.setFromDb', false);
	     
        $this->crud->addColumn([
          'name' => 'created_at',
          'label' => 'Дата'
        ]);
        
        $this->crud->addColumn([
          'name' => 'photo',
          'label' => 'Фото',
          'type' => 'image'
        ]); 
        
        $this->crud->addColumn([
          'name' => 'name',
          'label' => 'Имя',
        ]);
        
        $this->crud->addColumn([
          'name' => 'text',
          'label' => 'Текст',
        ]);
        
        $this->crud->addColumn([
          'name' => 'is_moderated',
          'label' => 'Опубликовано',
          'type' => 'check'
        ]);
        
        $this->crud->addColumn([
          'name' => 'likes',
          'label' => 'Likes',
        ]);
        
        $this->crud->addColumn([
          'name' => 'dislikes',
          'label' => 'Dislikes',
        ]);
        
        //$this->crud->removeColumns(['lft', 'depth']);
        // $this->crud->removeColumn('rgt');
    }
    
    protected function setupListOperation()
    {
        // TODO: remove setFromDb() and manually define Columns, maybe Filters
        
        // $this->crud->setFromDb();
        
        $this->crud->addColumn([
          'name' => 'photo',
          'label' => '',
          'type' => 'image'
        ]);
        
        $this->crud->addColumn([
          'name' => 'created_at',
          'label' => 'Дата'
        ]);
               
        $this->crud->addColumn([
          'name' => 'is_moderated',
          'label' => 'Опубликовано',
          'type' => 'check'
        ]);
        
        if(config('backpack.reviews.enable_review_type')) {
          $this->crud->addColumn([
            'name' => 'type',
            'label' => 'Тип',
          ]);
        }
      
        $this->crud->addColumn([
          'name' => 'owner',
          'label' => 'Автор',
          'type' => 'relationship',
          'attribute' => 'email'
        ]);
        
      // if(config('backpack.reviews.enable_review_for_product')) {
      //   $this->crud->addColumn([
      //     'name' => 'product_id',
      //     'label' => 'Приобретённый товар',
      //     'type' => 'select',
      //     'entity' => 'Product',
      //     'attribute' => 'name',
      //     'model' => "Aimix\Shop\app\Models\Product",
      //   ]);
      // }
        
      if(config('backpack.reviews.enable_rating')) {
        $this->crud->addColumn([
          'name' => 'rating',
          'label' => 'Оценка',
        ]);
      }
    }

    protected function setupCreateOperation()
    {
       // $this->crud->setValidation(ReviewRequest::class);

        // TODO: remove setFromDb() and manually define Fields
        // $this->crud->setFromDb();
        
        $this->crud->addField([
          'name' => 'is_moderated',
          'label' => 'Опубликовано',
          'type' => 'boolean'
        ]);
        
      if(config('backpack.reviews.enable_review_type')) {
        $this->crud->addField([
          'name' => 'type',
          'label' => 'Тип',
          'type' => 'select_from_array',
          'options' => [
            'text' => 'Текстовый',
            'video' => 'Видео'
          ]
        ]);
      }
        
        $this->crud->addField([
          'name' => 'owner',
          'label' => 'Автор',
          'type' => 'relationship',
          'attribute' => 'email'
        ]);
        
        
      if(config('backpack.reviews.enable_review_type')) {
        $this->crud->addField([
          'name' => 'file',
          'label' => 'Фото/видео',
          'type' => 'browse',
          'disc' => 'review',
        ]);
      } else {
        $this->crud->addField([
          'name' => 'file',
          'label' => 'Фото',
          'type' => 'browse',
          'disc' => 'review',
        ]);
      }
        
      // if(config('backpack.reviews.enable_review_for_product')) {
      //   $this->crud->addField([
      //     'name' => 'product_id',
      //     'label' => 'Приобретённый товар',
      //     'type' => 'select2',
      //     'entity' => 'Product',
      //     'attribute' => 'name',
      //     'model' => "Aimix\Shop\app\Models\Product",
      //   ]);
      // }
        
      if(config('backpack.reviews.enable_rating')) {
        $this->crud->addField([
          'name' => 'rating',
          'label' => 'Оценка',
          'type' => 'number',
          'attributes' => [
            'max' => '5',
            'min' => '0'
          ]
        ]);
      }

      if(config('backpack.reviews.enable_review_type')) {
        $this->crud->addField([
          'name' => 'text',
          'label' => 'Сообщение/html-код видео',
          'type' => 'textarea',
          'attributes' => [
            'rows' => '8'
          ]
        ]);
      } else {
        $this->crud->addField([
          'name' => 'text',
          'label' => 'Сообщение',
          'type' => 'textarea',
          'attributes' => [
            'rows' => '8'
          ]
        ]);
      }
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
