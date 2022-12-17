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
	
	  protected $with = ['user'];
	
    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    public function toArray()
    {
      return [
        "id" => $this->id,
        "is_moderated" => $this->is_moderated,
        "type" => $this->type,
        "name" => $this->name,
        "email" => $this->email,
        "category" => $this->category,
        "product_id" => $this->product_id,
        "children" => $this->children,
        "rating" => $this->rating,
        "likes" => $this->likes,
        "dislikes" => $this->dislikes,
        "files" => $this->files,
        "text" => $this->text,
        "photo" => url($this->photo),
        "created_at" => \Carbon\Carbon::createFromTimeStamp(strtotime($this->created_at))->diffForHumans()
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
      return $this->belongsTo(config('backpack.reviews.user_model', 'App\Models\User'), 'owner_id');
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

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */
    public function getPhotoAttribute(){
      
  /*
      if(is_object($this->user->usermeta->photo))
        dd($this->user->usermeta->photo);
  */
      
      if($this->name == 'Incognito')
        return url('/img/incognito.png');
    
      if($this->user && $this->user->usermeta && $this->user->usermeta->photo)
        return url($this->user->usermeta->photo);
      
      if($this->file)
        return url($this->file);
      else
        return url('/img/profile.png');	
    }
    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
