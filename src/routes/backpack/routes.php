<?php

Route::any('/admin/tag/inline/create/createOrAttach', 'Backpack\Tag\app\Http\Controllers\Admin\TagCrudController@createOrAttachTag');
Route::any('/admin/tag/inline/create/attach', 'Backpack\Tag\app\Http\Controllers\Admin\TagCrudController@attachTag');
Route::any('/admin/tag/inline/remove', 'Backpack\Tag\app\Http\Controllers\Admin\TagCrudController@detachTag');

Route::group([
  'prefix'     => config('backpack.base.route_prefix', 'admin'),
  'middleware' => ['web', config('backpack.base.middleware_key', 'admin')],
  'namespace'  => 'Backpack\Tag\app\Http\Controllers\Admin',
], function () { 
    Route::crud('tag', 'TagCrudController');
}); 

