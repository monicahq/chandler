<?php

namespace Database\Factories;

use App\Models\Vault;
use App\Models\Couple;
use Illuminate\Database\Eloquent\Factories\Factory;

class CoupleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Couple::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'vault_id' => Vault::factory(),
            'name' => $this->faker->name(),
        ];
    }
}
