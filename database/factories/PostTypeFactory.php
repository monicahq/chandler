<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\PostType;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = PostType::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'account_id' => Account::factory(),
            'label' => 'business',
            'position' => 1,
        ];
    }
}
