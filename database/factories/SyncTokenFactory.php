<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\SyncToken;
use App\Models\User;
use App\Models\Vault;
use Illuminate\Database\Eloquent\Factories\Factory;

class SyncTokenFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = SyncToken::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'account_id' => Account::factory(),
            'user_id' => User::factory(),
            'name' => Vault::factory()->create()->uuid,
            'timestamp' => $this->faker->dateTimeThisCentury,
        ];
    }
}
