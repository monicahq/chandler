<?php

namespace Database\Factories;

use App\Models\Contact;
use App\Models\ContactFeedItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactFeedItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = ContactFeedItem::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'author_id' => User::factory(),
            'contact_id' => Contact::factory(),
            'action' => ContactFeedItem::ACTION_NOTE_CREATED,
            'description' => $this->faker->word(),
        ];
    }
}
