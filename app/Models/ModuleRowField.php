<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ModuleRowField extends Model
{
    use HasFactory;

    protected $table = 'module_row_fields';

    /**
     * Possible module field types.
     */
    public const TYPE_INPUT_TEXT = 'input_text';

    public const TYPE_TEXTAREA = 'textarea';

    public const TYPE_DROPDOWN = 'dropdown';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'module_row_id',
        'label',
        'help',
        'placeholder',
        'module_field_type',
        'required',
        'position',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'required' => 'boolean',
    ];

    /**
     * Get the module row associated with the module row field.
     *
     * @return BelongsTo
     */
    public function row(): BelongsTo
    {
        return $this->belongsTo(ModuleRow::class, 'module_row_id');
    }

    /**
     * Get the module row field choices associated with the module row field.
     *
     * @return HasMany
     */
    public function choices(): HasMany
    {
        return $this->hasMany(ModuleRowFieldChoice::class);
    }
}
