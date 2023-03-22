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
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation  { update as traitUpdate; }
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    //use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup()
    {
        $this->crud->setModel('Backpack\Reviews\app\Models\Review');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/review');
        $this->crud->setEntityNameStrings('отзыв', 'отзывы');

        $this->reviewableList = config('backpack.reviews.reviewable_types_list', []);

        // CURRENT MODEL
        $this->setEntry();

        // if($this->crud->getCurrentOperation() === 'update' && \Request::query('reviewable_type')){
        //   $redirect_to = \Request::url();
        //   header("Location: {$redirect_to}");
        //   die();
        // }
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
      
      $js_attributes = [
        'data-value' => '',
        'onfocus' => "this.setAttribute('data-value', this.value);",
        'onchange' => "
            const value = event.target.value
            let isBoss = confirm('Несохраненные данные будут сброшены. Все равно продолжить?');
            
            if(isBoss) {
              reload_page(event);
            } else{
              this.value = this.getAttribute('data-value');
            }

            function reload_page(event) {
              const value = event.target.value
              url = insertParam('reviewable_type', value)
            };

            function insertParam(key, value) {
              key = encodeURIComponent(key);
              value = encodeURIComponent(value);
          
              // kvp looks like ['key1=value1', 'key2=value2', ...]
              var kvp = document.location.search.substr(1).split('&');
              let i=0;
          
              for(; i<kvp.length; i++){
                  if (kvp[i].startsWith(key + '=')) {
                      let pair = kvp[i].split('=');
                      pair[1] = value;
                      kvp[i] = pair.join('=');
                      break;
                  }
              }
          
              if(i >= kvp.length){
                  kvp[kvp.length] = [key,value].join('=');
              }
          
              // can return this or...
              let params = kvp.join('&');
          
              // reload page with new params
              document.location.search = params;
          }
          "
      ];

      $this->crud->addField([
        'name' => 'reviewable_type',
        'label' => 'Тип',
        'type' => 'select_from_array',
        'options' => $this->reviewableList,
        'value' => $this->getReviewableType(),
        'attributes' => $js_attributes
      ]);

      $this->crud->addField([
        'name' => 'reviewable_id',
        'label' => 'Id',
        'type' => "relationship",
        'model' => $this->getReviewableType(),
      ]); 
        
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
        'name' => 'parent',
        'label' => 'Родительский комментарий',
        'type' => 'relationship',
        'attribute' => 'shortIdentity'
      ]);


      $this->crud->addField([
        'name' => 'owner',
        'label' => 'Автор',
        'type' => 'relationship',
        'attribute' => 'email',
        'hint' => 'Cсылка на зарегистрированного пользователя'
      ]);

      $this->crud->addField([
        'name'  => 'separator',
        'type'  => 'custom_html',
        'value' => '<hr>'
      ]);

      $this->crud->addField([
        'name'  => 'caption',
        'type'  => 'custom_html',
        'value' => '<h5>Автор (статические данные)</h5>'
      ]);

      $this->crud->addField([
          'name' => 'ownerId',
          'label' => 'Id автора',
          'type' => 'number',
          'wrapper' => [ 
            'class' => 'form-group col-md-4'
          ]
      ]);
      $this->crud->addField([
          'name' => 'ownerFullname',
          'label' => 'Имя автора',
          'type'  => 'text',
          'wrapper' => [ 
            'class' => 'form-group col-md-8'
          ]
      ]);
      $this->crud->addField([
          'name' => 'ownerEmail',
          'label' => 'Email автора',
          'type'  => 'email',
          'wrapper' => [ 
            'class' => 'form-group col-md-4'
          ]
      ]);
      $this->crud->addField([
          'name' => 'ownerPhoto',
          'label' => 'Фото автора',
          'type'  => 'browse',
          'wrapper' => [ 
            'class' => 'form-group col-md-8'
          ]
      ]);

      $this->crud->addField([
        'name'  => 'separator_2',
        'type'  => 'custom_html',
        'value' => '<hr>'
      ]);

      // $this->crud->addField([
      //   'name' => 'user',
      //   'label' => 'Автор',
      //   'type'  => 'repeatable',
      //   'fields' => [
      //       [
      //           'name'    => 'name',
      //           'type'    => 'text',
      //           'label'   => 'Name',
      //           'wrapper' => ['class' => 'form-group col-md-4'],
      //       ],
      //       [
      //           'name'    => 'position',
      //           'type'    => 'text',
      //           'label'   => 'Position',
      //           'wrapper' => ['class' => 'form-group col-md-4'],
      //       ],
      //       [
      //           'name'    => 'company',
      //           'type'    => 'text',
      //           'label'   => 'Company',
      //           'wrapper' => ['class' => 'form-group col-md-4'],
      //       ],
      //       [
      //           'name'  => 'quote',
      //           'type'  => 'ckeditor',
      //           'label' => 'Quote',
      //       ],
      //   ],
      //   'fake'     => true, 
      //   'store_in' => 'extras',
      //   'init_rows' => 1, // number of empty rows to be initialized, by default 1
      //   'min_rows' => 1, // minimum rows allowed, when reached the "delete" buttons will be hidden
      //   'max_rows' => 1
      // ]);

        
        
      // if(config('backpack.reviews.enable_review_type')) {
      //   $this->crud->addField([
      //     'name' => 'file',
      //     'label' => 'Фото/видео',
      //     'type' => 'browse',
      //     'disc' => 'review',
      //   ]);
      // } else {
      //   $this->crud->addField([
      //     'name' => 'file',
      //     'label' => 'Фото',
      //     'type' => 'browse',
      //     'disc' => 'review',
      //   ]);
      // }
        
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
          ],
          'wrapper' => [ 
            'class' => 'form-group col-md-4'
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


      $this->crud->addField([
        'name'  => 'separator_3',
        'type'  => 'custom_html',
        'value' => '<hr>'
      ]);

      $this->crud->addField([
        'name'  => 'caption_2',
        'type'  => 'custom_html',
        'value' => '<h5>Данные сгенерированные пользователями</h5>'
      ]);

      $this->crud->addField([
        'name' => 'likes',
        'label' => 'Лайки',
        'type' => 'number',
        'wrapper' => [ 
          'class' => 'form-group col-md-4'
        ]
      ]);

      $this->crud->addField([
        'name' => 'dislikes',
        'label' => 'Дизлайки',
        'type' => 'number',
        'wrapper' => [ 
          'class' => 'form-group col-md-4'
        ]
      ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    private function setEntry() {
      if($this->crud->getCurrentOperation() === 'update')
        $this->entry = $this->crud->getEntry(\Route::current()->parameter('id'));
      else
        $this->entry = null;
    }

    private function getReviewableType() {
      if(\Request::get('reviewable_type'))
        return \Request::get('reviewable_type');
      elseif($this->entry && $this->entry->reviewable_type)
        return $this->entry->reviewable_type;
      else
        return null;

    }

    public function update($request){
      $requestData = \Request::all();
      $requestData['http_referrer'] = 'https://google.com';

      $response = $this->traitUpdate();
      return $response;
    }
}
