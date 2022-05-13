<?php

namespace Database\Factories;

use App\Models\Contact;
use App\Models\ContactTask;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactTaskFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ContactTask::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'contact_id' => Contact::factory(),
            'label' => $this->faker->sentence(),
        ];
    }
}
