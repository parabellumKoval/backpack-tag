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
      // 'extrasOwnerFullname',
      // 'extrasOwnerPhoto',
      // 'extrasOwnerEmail',
      // 'extrasOwnerId',
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

    /**
     * __construct
     *
     * @param  mixed $attributes
     * @return void
     */
    // public function __construct(array $attributes = array()) {
    //   parent::__construct($attributes);
    // }

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

    public function scopeModerated($query)
    {
      return $query->where('is_moderated', 1);
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */    
    /**
     * getShortReviewableAttribute
     *
     * @return void
     */
    public function getShortReviewableAttribute() {
      if(!$this->reviewable)
        return null;
      
      return [
        'id' => $this->reviewable->id,
        'name' => $this->reviewable->name,
        'slug' => $this->reviewable->slug,
        'class' => get_class($this->reviewable)
      ];
    }
    
    /**
     * getDetailedRatingAvrAttribute
     *
     * @return void
     */
    public function getDetailedRatingAvrAttribute() {
      if(isset($this->extras['rating']) && count($this->extras['rating'])) {
        return array_sum($this->extras['rating']) / count($this->extras['rating']);
      }else{
        return 0;
      }
    }
    
    /**
     * getOwnerModelOrInfoAttribute
     *
     * @return void
     */
    public function getOwnerModelOrInfoAttribute() {
      if(isset($this->extras['owner'])){
        return $this->extras['owner'];
      }elseif($this->user){
        return $this->user;
      }else {
        return null;
      }
    }
    
    /**
     * getShortIdentityAttribute
     *
     * @return void
     */
    public function getShortIdentityAttribute() {
      $identity_string = "id - {$this->id}";

      if(!empty($this->text))
        $identity_string .= " / " . substr($this->text, 0, 70) . '...';

      return $identity_string;
    }
    
    /**
     * getPhotoAnywayAttribute
     *
     * @return void
     */
    public function getPhotoAnywayAttribute() {
      if($this->user && $this->user->photo){
        return $this->user->photo;
      }else if($this->extrasOwnerPhoto) {
        return $this->extrasOwnerPhoto;
      }else {
        return null;
      }
    }

    public function getOwnerAttribute() {
      return [$this->extras['owner']];
    }

    public function getExtrasOwnerIdAttribute() {
      return $this->extras['owner'][0]['id'] ?? null;
    }

    public function getExtrasOwnerFullnameAttribute() {
      return $this->extras['owner'][0]['name'] ?? null;
    }
    
    public function getExtrasOwnerEmailAttribute() {
      return $this->extras['owner'][0]['email'] ?? null;
    }
    
    public function getExtrasOwnerPhotoAttribute() {
      return $this->extras['owner']['photo'] ?? null;
    }

    // public function getOwnerAttribute() {
    //   dd($this->extras['owner']);
    // }
    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */

    // public function setOwnerAttribute($value) {
    //   // dd(['owner', $value]);
    //   // echo 'set';
    // }

    // public function setOwnerIdAttribute($value) {
    //   $extras = $this->extras;
    //   $extras['owner']['id'] = $value;
    //   $this->extras = $extras;
    // }
    
    // public function setExtrasOwnerEmailAttribute($value) {
    //   dd(['ownerEmail', $value]);
    //   $extras = $this->extras;
    //   $extras['owner']['email'] = $value;
    //   $this->extras = $extras;
    // }
    
    // public function setOwnerFullnameAttribute($value) {
    //   dd(['ownerFullname', $value]);
    //   $extras = $this->extras;
    //   $extras['owner']['name'] = $value;
    //   $this->extras = $extras;
    // }

    // public function setExtrasOwnerPhotoAttribute($value) {
    //   $extras = $this->extras;
    //   $extras['owner']['photo'] = $value;
    //   $this->extras = $extras;
    // }
}
