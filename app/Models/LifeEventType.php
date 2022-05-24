<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LifeEventType extends Model
{
    use HasFactory;

    protected $table = 'life_event_types';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'life_event_category_id',
        'label',
        'label_translation_key',
    ];

    /**
     * Get the life event category associated with the life event type.
     *
     * @return BelongsTo
     */
    public function lifeEventCategory()
    {
        return $this->belongsTo(LifeEventCategory::class, 'life_event_category_id');
    }

    /**
     * Get the life events associated with the life event type.
     *
     * @return HasMany
     */
    public function lifeEvents()
    {
        return $this->hasMany(LifeEvent::class);
    }
}
