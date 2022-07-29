<?php

namespace Database\Factories;

use App\Models\Avatar;
use App\Models\Contact;
use App\Models\File;
use Illuminate\Database\Eloquent\Factories\Factory;

class AvatarFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Avatar::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'contact_id' => Contact::factory(),
            'file_id' => File::factory(),
            'type' => Avatar::TYPE_GENERATED,
            'svg' => '123',
        ];
    }
}
