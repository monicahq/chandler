<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\CallReasonType;
use App\Models\Vault;
use Illuminate\Database\Eloquent\Factories\Factory;

class CallReasonTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CallReasonType::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'account_id' => Account::factory(),
            'label' => $this->faker->name(),
        ];
    }
}
