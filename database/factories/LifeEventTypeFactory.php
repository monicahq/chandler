<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\ActivityType;
use App\Models\LifeEventCategory;
use App\Models\LifeEventType;
use Illuminate\Database\Eloquent\Factories\Factory;

class LifeEventTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LifeEventType::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'life_event_category_id' => LifeEventCategory::factory(),
            'label' => $this->faker->name(),
            'label_translation_key' => $this->faker->name(),
            'can_be_deleted' => true,
        ];
    }
}
