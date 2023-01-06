<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModuleRowFieldChoice extends Model
{
    use HasFactory;

    protected $table = 'module_row_field_choices';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'module_row_field_id',
        'label',
    ];

    /**
     * Get the module row field associated with the module row field choice.
     *
     * @return BelongsTo
     */
    public function rowField(): BelongsTo
    {
        return $this->belongsTo(ModuleRowField::class, 'module_row_field_id');
    }
}
