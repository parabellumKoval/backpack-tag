<?php

namespace Backpack\Tag\app\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

use Backpack\Tag\app\Http\Requests\TagRequest;
use Backpack\Tag\app\Models\Tag;

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

    // use \App\Http\Controllers\Admin\Traits\TagCrud;

    public function setup()
    {
      $this->crud->setModel(Tag::class);
      $this->crud->setRoute(config('backpack.base.route_prefix') . '/tag');
      $this->crud->setEntityNameStrings('tag', 'tag');

    }

    protected function setupShowOperation()
    {
    }
    
    protected function setupListOperation()
    {          
      $this->crud->addColumn([
        'name' => 'text',
        'label' => 'Text',
        'type' => 'check'
      ]);

    }

    protected function setupCreateOperation()
    {
       $this->crud->setValidation(TagRequest::class);
        
      $this->crud->addField([
        'name' => 'text',
        'label' => 'Text',
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

}
