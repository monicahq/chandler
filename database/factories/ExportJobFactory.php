<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\ExportJob;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ExportJob>
 */
class ExportJobFactory extends Factory
{
    protected $model = ExportJob::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'account_id' => Account::factory(),
            'user_id' => fn (array $attributes) => User::factory([
                'account_id' => $attributes['account_id'],
            ]),
            'type' => 'json',
        ];
    }
}
