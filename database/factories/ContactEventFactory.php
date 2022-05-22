<?php

namespace Database\Factories;

use App\Models\Activity;
use App\Models\Contact;
use App\Models\ContactActivity;
use App\Models\ContactEvent;
use App\Models\Emotion;
use App\Models\Vault;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactEventFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ContactEvent::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'contact_id' => Contact::factory(),
            'summary' => $this->faker->word(),
            'started_at' => $this->faker->dateTimeThisCentury(),
            'ended_at' => $this->faker->dateTimeThisCentury(),
        ];
    }
}
