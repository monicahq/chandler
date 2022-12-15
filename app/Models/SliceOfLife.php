<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SliceOfLife extends Model
{
    use HasFactory;

    protected $table = 'slices_of_life';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'journal_id',
        'name',
    ];

    /**
     * Get the journal associated with the slice of life.
     *
     * @return BelongsTo
     */
    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class);
    }
}
