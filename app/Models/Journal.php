<?php

namespace App\Models;

use App\Helpers\ScoutHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Scout\Attributes\SearchUsingFullText;
use Laravel\Scout\Attributes\SearchUsingPrefix;
use Laravel\Scout\Searchable;

class Journal extends Model
{
    use HasFactory;
    use Searchable;

    protected $table = 'journals';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'vault_id',
        'name',
        'description',
    ];

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     * @codeCoverageIgnore
     */
    #[SearchUsingPrefix(['id', 'vault_id'])]
    #[SearchUsingFullText(['name'])]
    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'vault_id' => $this->vault_id,
            'name' => $this->name,
        ];
    }

    /**
     * When updating a model, this method determines if we should update the search index.
     *
     * @return bool
     */
    public function searchIndexShouldBeUpdated()
    {
        return ScoutHelper::activated();
    }

    /**
     * Get the vault associated with the group.
     *
     * @return BelongsTo
     */
    public function vault(): BelongsTo
    {
        return $this->belongsTo(Vault::class);
    }
}
