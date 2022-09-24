<?php

namespace Database\Factories;

use App\Models\PostType;
use App\Models\PostTypeSection;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostTypeSectionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = PostTypeSection::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'post_type_id' => PostType::factory(),
            'label' => 'business',
            'position' => 1,
        ];
    }
}
