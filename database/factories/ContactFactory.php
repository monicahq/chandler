<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Contact;
use App\Models\Vault;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Contact::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'vault_id' => Vault::factory(),
            'first_name' => 'Regis',
            'last_name' => 'Troyat',
            'middle_name' => $this->faker->firstName,
            'nickname' => $this->faker->unique()->firstName,
            'maiden_name' => $this->faker->unique()->name,
            'can_be_deleted' => true,
            'company_id' => Company::factory(),
        ];
    }
}
