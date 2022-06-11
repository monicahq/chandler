<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Couple extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'vault_id',
        'name',
    ];

    /**
     * Get the vault associated with the couple.
     *
     * @return BelongsTo
     */
    public function vault()
    {
        return $this->belongsTo(Vault::class);
    }
}
