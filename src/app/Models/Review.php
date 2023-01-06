<?php

namespace Backpack\Reviews\app\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

// FACTORY
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Backpack\Reviews\database\factories\ReviewFactory;

class Review extends Model
{
    use CrudTrait;
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'ak_reviews';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];
	
	  protected $with = ['owner'];

    protected $casts = [
      'extras' => 'array',
    ];
	
    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    public function toArray()
    {
      return [
        "id" => $this->id,
        "owner" => $this->ownerModelOrInfo,
        "rating" => $this->rating,
        "likes" => $this->likes,
        "dislikes" => $this->dislikes,
        "text" => $this->text,
        "extras" => $this->extras,
        "created_at" => $this->created_at,
        "children" => $this->children,
      ];
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
      return ReviewFactory::new();
    }
    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    
    public function owner()
    {
      return $this->belongsTo(config('backpack.reviews.owner_model', 'Backpack\Profile\app\Models\Profile'), 'owner_id');
    }
    
    public function parent()
    {
      return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
      return $this->hasMany(self::class, 'parent_id');
    }
    
    public function reviewable()
    {
      return $this->morphTo();
    }
    
    // public function transaction() {
    //   return $this->hasOne('Backpack\Account\app\Models\Transaction');
    // }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */
    public function scopeRoot($query)
    {
      return $query->where('parent_id', 0);
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */
    public function getDetailedRatingAvrAttribute() {
      if(isset($this->extras['rating']) && count($this->extras['rating'])) {
        return array_sum($this->extras['rating']) / count($this->extras['rating']);
      }else{
        return 0;
      }
    }

    public function getOwnerModelOrInfoAttribute() {
      if($this->owner) {
        return $this->owner;
      }else if(isset($this->extras['owner'])){
        return $this->extras['owner'];
      }else {
        return null;
      }
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
