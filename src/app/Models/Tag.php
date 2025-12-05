<?php

namespace Backpack\Tag\app\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

// TRANSLATIONS
use Backpack\CRUD\app\Models\Traits\SpatieTranslatable\HasTranslations;

// FACTORY
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Backpack\Reviews\database\factories\ReviewFactory;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tag extends Model
{
    use CrudTrait;
    use HasFactory;
    use HasTranslations;
    
    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'ak_tags';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    protected $fillable = [
      'value',
      'label',
      'color',
      'icon',
      'extras'
    ];
    // protected $hidden = [];
    // protected $dates = [];
	
    // !!!!
	  // protected $with = ['owner'];

    protected $casts = [
      'extras' => 'array',
    ];

    protected $translatable = ['label'];
	
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
        "value" => $this->value,
        "label" => $this->label,
        "color" => $this->color,
        "icon" => $this->icon,
        "extras" => $this->extras,
        "created_at" => $this->created_at
      ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /**
     * Get models of a specific type that are tagged with this tag.
     */
    public function getTaggedModels(string $modelClass)
    {
        return $this->morphedByMany($modelClass, 'taggable', 'ak_taggables')
            ->withPivot('id')
            ->withTimestamps();
    }

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

    public function getColorAdminAttribute() {
      $html = '<div style="background: ' . $this->color . '; width: 25px; height:25px; border-radius: 100%;"></div>';
      return $html;
    }
    
    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | SERVICE OPERATION
    |--------------------------------------------------------------------------
    */
    public function getServiceMergeConfiguration(): array
    {
        return [
            'label' => 'Слияние тегов',
            'description' => 'Перенесите переводы и привязки дублирующегося тега.',
            'candidate_search' => ['label', 'value', 'id'],
            'fields' => [
                'label' => [
                    'label' => 'Название',
                    'strategy' => 'translations',
                    'default' => true,
                    'help' => 'Добавляет отсутствующие переводы в целевом теге.',
                ],
                'value' => [
                    'label' => 'Slug',
                    'strategy' => 'replace',
                ],
                'color' => [
                    'label' => 'Цвет',
                    'strategy' => 'replace',
                ],
                'extras' => [
                    'label' => 'Дополнительные данные',
                    'strategy' => 'append',
                ],
            ],
            'relations' => [
                'taggables' => [
                    'label' => 'Все привязки (ak_taggables)',
                    'type' => 'table',
                    'table' => 'ak_taggables',
                    'column' => 'tag_id',
                    'primary_key' => 'id',
                    'unique' => ['taggable_type', 'taggable_id'],
                    'default' => true,
                    'help' => 'Заменит ID тега во всех прикреплённых сущностях и удалит дубли.',
                ],
            ],
        ];
    }
}
