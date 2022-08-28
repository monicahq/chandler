<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\GroupType;
use App\Models\Vault;
use Illuminate\Database\Eloquent\Factories\Factory;

class GroupFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = Group::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'vault_id' => Vault::factory(),
            'group_type_id' => GroupType::factory(),
            'name' => $this->faker->name(),
        ];
    }
}
