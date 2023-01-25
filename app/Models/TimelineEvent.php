<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A contact's timeline is composed of many timeline events.
 * A timeline event is something that happened to one or more contacts.
 * A timeline event can cover multiple days, if needed, like "trip to antartica".
 * It is composed of one or more life events, like "drove 100 km", "ate pizza"
 * or whatever.
 */
class TimelineEvent extends Model
{
    use HasFactory;

    protected $table = 'timeline_events';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'vault_id',
        'started_at',
        'label',
        'collapsed',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'started_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'collapsed' => 'boolean',
    ];

    /**
     * Get the vault associated with the timeline event.
     *
     * @return BelongsTo
     */
    public function vault(): BelongsTo
    {
        return $this->belongsTo(Vault::class);
    }

    /**
     * Get the life events associated with the timeline event.
     *
     * @return HasMany
     */
    public function lifeEvents(): HasMany
    {
        return $this->hasMany(LifeEvent::class);
    }
}
