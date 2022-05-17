<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Contact;
use App\Models\Pet;
use App\Models\PetCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class PetFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Pet::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'pet_category_id' => PetCategory::factory(),
            'contact_id' => Contact::factory(),
            'name' => $this->faker->name(),
        ];
    }
}
