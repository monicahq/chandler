<?php

namespace Database\Factories;

use App\Models\Contact;
use App\Models\ContactEvent;
use App\Models\LifeEvent;
use App\Models\LifeEventType;
use Illuminate\Database\Eloquent\Factories\Factory;

class LifeEventFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LifeEvent::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'life_event_type_id' => LifeEventType::factory(),
            'summary' => $this->faker->word(),
            'started_at' => $this->faker->dateTimeThisCentury(),
            'ended_at' => $this->faker->dateTimeThisCentury(),
        ];
    }
}
