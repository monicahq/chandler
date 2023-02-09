<?php

namespace Database\Factories;

use App\Models\VaultQuickFactTemplate;
use App\Models\Vault;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VaultQuickFactTemplate>
 */
class VaultQuickFactTemplateFactory extends Factory
{
    protected $model = VaultQuickFactTemplate::class;

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
            'position' => 1,
        ];
    }
}
