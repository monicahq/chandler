<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pronoun extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'account_id',
        'name',
    ];

    /**
     * Get the account associated with the gender.
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}
