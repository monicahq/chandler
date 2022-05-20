<?php

namespace Database\Factories;

use App\Models\Activity;
use App\Models\ContactActivity;
use App\Models\Emotion;
use App\Models\Loan;
use App\Models\Vault;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactActivityFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ContactActivity::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'vault_id' => Vault::factory(),
            'activity_id' => Activity::factory(),
            'emotion_id' => Emotion::factory(),
            'summary' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'happened_at' => $this->faker->date(),
            'period_of_day' => ContactActivity::TYPE_PERIOD_AFTERNOON,
        ];
    }
}
