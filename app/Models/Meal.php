<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Meal extends Model
{
    use HasFactory;

    protected $table = 'meals';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'vault_id',
        'meal_category_id',
        'name',
        'url_to_recipe',
        'time_to_prepare_in_minutes',
        'time_to_cook_in_minutes',
    ];

    /**
     * Get the vault associated with the meal.
     */
    public function vault(): BelongsTo
    {
        return $this->belongsTo(Vault::class);
    }

    /**
     * Get the ingredients associated with the meal.
     */
    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(Ingredient::class)->withPivot('quantity', 'position');
    }

    /**
     * Get the meal category associated with the meal.
     */
    public function mealCategory(): BelongsTo
    {
        return $this->belongsTo(MealCategory::class);
    }
}
