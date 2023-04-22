<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JournalMetric extends Model
{
    use HasFactory;

    protected $table = 'journal_metrics';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'journal_id',
        'label',
    ];

    /**
     * Get the journal associated with the journal metric.
     */
    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class);
    }

    /**
     * Get the post metrics associated with the journal metric.
     */
    public function postMetrics(): HasMany
    {
        return $this->hasMany(PostMetric::class);
    }
}
