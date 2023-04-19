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
    protected $fillable = [
      'text',
      'is_moderated',
      'rating',
      'likes',
      'dislikes',
      'owner_id',
      'parent_id',
      'reviewable_type', 
      'reviewable_id',
      'extrasOwnerFullname',
      'extrasOwnerPhoto',
      'extrasOwnerEmail',
      'extrasOwnerId',
      'extras'
    ];
    // protected $hidden = [];
    // protected $dates = [];
	
    // !!!!
	  // protected $with = ['owner'];

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
        "likes" => $this->likes? $this->likes: 0,
        "dislikes" => $this->dislikes? $this->dislikes: 0,
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
    
    public function user()
    {
      $model = config('backpack.reviews.owner_model', null);

      if(!$model)
        return null;

      return $this->belongsTo($model, 'owner_id');
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

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */
    public function scopeRoot($query)
    {
      return $query->where('parent_id', 0)->orWhere('parent_id', null);
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
      if(isset($this->extras['owner'])){
        return $this->extras['owner'];
      }elseif($this->user){
        return $this->user;
      }else {
        return null;
      }
    }

    public function getShortIdentityAttribute() {
      $identity_string = "id - {$this->id}";

      if(!empty($this->text))
        $identity_string .= " / " . substr($this->text, 0, 70) . '...';

      return $identity_string;
    }

    public function getPhotoAnywayAttribute() {
      if($this->ownerPhoto)
        return $this->ownerPhoto;
      else
        null;
    }

    public function getExtrasOwnerIdAttribute() {
      return $this->extras['owner']['id'] ?? null;
    }

    public function getExtrasOwnerFullnameAttribute() {
      return $this->extras['owner']['fullname'] ?? null;
    }
    
    public function getExtrasOwnerEmailAttribute() {
      return $this->extras['owner']['email'] ?? null;
    }
    
    public function getExtrasOwnerPhotoAttribute() {
      return $this->extras['owner']['photo'] ?? null;
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */

    public function setExtrasOwnerIdAttribute($value) {
      $extras = $this->extras;
      $extras['owner']['id'] = $value;
      $this->extras = $extras;
    }
    
    public function setExtrasOwnerEmailAttribute($value) {
      $extras = $this->extras;
      $extras['owner']['email'] = $value;
      $this->extras = $extras;
    }
    
    public function setExtrasOwnerFullnameAttribute($value) {
      $extras = $this->extras;
      $extras['owner']['fullname'] = $value;
      $this->extras = $extras;
    }

    public function setExtrasOwnerPhotoAttribute($value) {
      $extras = $this->extras;
      $extras['owner']['photo'] = $value;
      $this->extras = $extras;
    }
}
