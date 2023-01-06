<?php

namespace Database\Factories;

use App\Models\ModuleRowField;
use App\Models\ModuleRowFieldChoice;
use Illuminate\Database\Eloquent\Factories\Factory;

class ModuleRowFieldChoiceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = ModuleRowFieldChoice::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'module_row_field_id' => ModuleRowField::factory(),
            'label' => $this->faker->word,
        ];
    }
}
