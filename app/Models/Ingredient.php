<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Ingredient extends Model
{
    use HasFactory;

    protected $table = 'ingredients';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'vault_id',
        'label',
        'label_translation_key',
    ];

    /**
     * Get the vault associated with the meal.
     */
    public function vault(): BelongsTo
    {
        return $this->belongsTo(Vault::class);
    }

    /**
     * Get the meals associated with the ingredient.
     */
    public function meals(): BelongsToMany
    {
        return $this->belongsToMany(Meal::class)->withPivot('quantity', 'position');
    }

    /**
     * Get the name of the ingredient.
     * The name is either the default name that we get from the translation key,
     * or the name that the user has entered.
     *
     * @return Attribute<string,string>
     */
    protected function label(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                if (! $value) {
                    return trans($attributes['label_translation_key']);
                }

                return $value;
            }
        );
    }
}
