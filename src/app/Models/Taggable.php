<?php

namespace Backpack\Tag\app\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Taggable extends Model
{
    use CrudTrait;
    
    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'ak_taggables';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    protected $fillable = [
      'tag_id',
      'taggable_id',
      'taggable_type'
    ];
    // protected $hidden = [];
    // protected $dates = [];
	
    // !!!!
	  // protected $with = [''];

    // protected $casts = [];
	
    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /**
     * __construct
     *
     * @param  mixed $attributes
     * @return void
     */
    // public function __construct(array $attributes = array()) {
    //   parent::__construct($attributes);
    // }

    // public function toArray()
    // {
    //   return [];
    // }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */   
    
    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */

}
