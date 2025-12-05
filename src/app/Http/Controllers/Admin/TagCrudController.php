<?php

namespace Backpack\Tag\app\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

use Illuminate\Http\Request;

use Backpack\Tag\app\Http\Requests\TagRequest;
use Backpack\Tag\app\Models\Tag;
use Backpack\Tag\app\Models\Taggable;

/**
 * Class TagCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class TagCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    // use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation  { update as traitUpdate; }
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    //use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\FetchOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\InlineCreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ServiceOperation;

    // use \App\Http\Controllers\Admin\Traits\TagCrud;

    public $opr;
    public $tags;

    public function setup()
    {
      $this->crud->setModel(Tag::class);
      $this->crud->setRoute(config('backpack.base.route_prefix') . '/tag');
      $this->crud->setEntityNameStrings('тег', 'теги');


      $this->opr = $this->crud->getCurrentOperation();
      $this->tags = Tag::all()->pluck('label', 'id')->toArray();
    }

    protected function setupShowOperation()
    {
    }
    
    protected function setupListOperation()
    {    

      $this->crud->addColumn([
        'name' => 'icon',
        'label' => 'Иконка',
        'type' => 'image',
        // 'value' => function($entry) {
        //   if (!empty($entry->icon)) {
        //     return '<img src="' . $entry->icon . '" style="width: 26px; height: 26px; object-fit: cover;" />';
        //   }
        //   return '—';
        // },
        'escaped' => false,
      ]);
            
      $this->crud->addColumn([
        'name' => 'label',
        'label' => 'Название',
        'type' => 'text_progress'
      ]);

      $this->crud->addColumn([
        'name' => 'value',
        'label' => 'Значение (slug)',
      ]);

      $this->crud->addColumn([
        'name' => 'colorAdmin',
        'label' => 'Цвет',
        'escaped' => false,
        'limit' => 2500,
        'searchLogic' => true,
      ]);

    }

    protected function setupCreateOperation()
    {
       $this->crud->setValidation(TagRequest::class);
          
       $field_label = [
        'name' => 'label',
        'label' => 'Название',
        'type' => 'text',
       ];

       $field_value = [
        'name' => 'value',
        'label' => 'Значение (slug)',
        'type' => 'text',
        'hint' => 'Уникальный идентификатор тега (латиница, цифры, дефисы)',
       ];

       $field_color = [
        'name' => 'color',
        'label' => 'Цвет',
        'type' => 'color',
       ];

       $field_icon = [
        'name' => 'icon',
        'label' => 'Иконка',
        'filename' => null,
        'type' => 'base64_image',
        'hint' => 'Маленькая картинка для отображения рядом с тегом (рекомендуется 26x26px)',
        'crop' => true,
        'aspect_ratio' => 1,
       ];

       if($this->opr === 'InlineCreate') {
        $field_label['tab'] = $field_value['tab'] = $field_color['tab'] = $field_icon['tab'] = 'Создать новый тег';
       }


      if($this->opr === 'InlineCreate') {  
        $this->crud->addField([
          'name' => 'select_tag',
          'label' => 'Выберите тег',
          'type' => 'select_from_array',
          'options' => $this->tags,
          'tab' => 'Выбрать существующий тег'
        ]);
      }

      $this->crud->addField($field_label);
      $this->crud->addField($field_value);
      $this->crud->addField($field_color);
      $this->crud->addField($field_icon);
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
    
    
    /**
     * createOrAttachTag
     *
     * @param  mixed $request
     * @return void
     */
    public function createOrAttachTag(Request $request) {
      $tag_id = $request->input('select_tag');

      $taggable_id = $request->input('taggable_id');
      $taggable_type = $request->input('taggable_type');

      // create
      if(empty($tag_id)) {
        $data_label = $request->input('label');
        $data_value = $request->input('value');
        $data_color = $request->input('color');
        $data_icon = $request->input('icon');
        
        $tag = Tag::create([
          'label' => $data_label,
          'value' => $data_value,
          'color' => $data_color,
          'icon' => $data_icon
        ]);

        $tag_id = $tag->id;
      }else {
        $tag = Tag::find($tag_id);
      }

      $taggable = Taggable::create([
        'tag_id' => $tag_id,
        'taggable_id' => $taggable_id,
        'taggable_type' => $taggable_type
      ]);

      return [
        'tag' => $tag,
        'taggable' => $taggable
      ];
    }

    /**
     * attachTag
     *
     * @param  mixed $request
     * @return void
     */
    public function attachTag(Request $request) {
      $taggable_id = $request->input('taggable_id');
      $taggable_type = $request->input('taggable_type');
      $tag_id = $request->input('tag_id');

      $taggable = Taggable::create([
        'tag_id' => $tag_id,
        'taggable_id' => $taggable_id,
        'taggable_type' => $taggable_type
      ]);

      return $taggable;
    }

    
    /**
     * detachTag
     *
     * @param  mixed $request
     * @return void
     */
    public function detachTag(Request $request) {
      $id = $request->input('id');

      try {
        Taggable::find($id)->delete();
        return true;
      }catch(\Exception $e) {
        return false;
      }
    }
}
