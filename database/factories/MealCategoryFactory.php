<?php

namespace Database\Factories;

use App\Models\MealCategory;
use App\Models\Vault;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MealCategory>
 */
class MealCategoryFactory extends Factory
{
    protected $model = MealCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'vault_id' => Vault::factory(),
            'position' => 1,
            'label' => $this->faker->name(),
        ];
    }
}
