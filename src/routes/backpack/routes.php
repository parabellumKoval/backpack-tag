<?php

Route::group([
  'prefix'     => config('backpack.base.route_prefix', 'admin'),
  'middleware' => ['web', config('backpack.base.middleware_key', 'admin')],
  'namespace'  => 'Backpack\Tag\app\Http\Controllers\Admin',
], function () { 
    Route::crud('review', 'TagCrudController');
}); 

