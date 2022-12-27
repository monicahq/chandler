<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\ContactInformationType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ContactInformationType>
 */
class ContactInformationTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = ContactInformationType::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'account_id' => Account::factory(),
            'name' => $this->faker->name(),
            'protocol' => '+tel',
            'can_be_deleted' => false,
            'type' => 'email',
        ];
    }
}
