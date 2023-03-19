<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\User;
use App\Models\Account\ExportJob;
use Illuminate\Database\Eloquent\Factories\Factory;

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
            'user_id' => function (array $attributes) {
                return User::factory([
                    'account_id' => $attributes['account_id'],
                ]);
            },
            'type' => 'json',
        ];
    }
}
