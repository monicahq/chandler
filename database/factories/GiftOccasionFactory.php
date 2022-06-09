<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Call;
use App\Models\CallReason;
use App\Models\Contact;
use App\Models\GiftOccasion;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class GiftOccasionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = GiftOccasion::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'account_id' => Account::factory(),
            'label' => $this->faker->name,
            'position' => 1,
        ];
    }
}
