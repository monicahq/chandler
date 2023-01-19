<?php

namespace Database\Factories;

use App\Models\Contact;
use App\Models\ContactTimeline;
use App\Models\LifeEvent;
use App\Models\LifeEventType;
use App\Models\TimelineEvent;
use App\Models\Vault;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TimelineEvent>
 */
class TimelineEventFactory extends Factory
{
    protected $model = TimelineEvent::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'vault_id' => Vault::factory(),
            'label' => $this->faker->sentence(),
            'started_at' => $this->faker->dateTimeThisCentury(),
        ];
    }
}
