<?php

namespace Tests\Unit\Domains\Settings\CreateAccount\Services;

use App\Domains\Settings\CreateAccount\Jobs\SetupAccount;
use App\Models\Currency;
use App\Models\RelationshipGroupType;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SetupAccountTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_sets_an_account_up(): void
    {
        Mail::fake();
        $user = $this->createAdministrator();

        $request = [
            'account_id' => $user->account->id,
            'author_id' => $user->id,
        ];

        SetupAccount::dispatchSync($request);

        $currency = Currency::first();

        $this->assertDatabaseHas('account_currency', [
            'currency_id' => $currency->id,
            'account_id' => $user->account_id,
        ]);
        $this->assertEquals(
            164,
            Currency::count()
        );

        $this->assertDatabaseHas('user_notification_channels', [
            'user_id' => $user->id,
            'label' => trans('app.notification_channel_email'),
            'type' => 'email',
            'content' => $user->email,
            'active' => true,
        ]);

        $this->assertDatabaseHas('templates', [
            'account_id' => $user->account_id,
            'name_translation_key' => 'Default template',
        ]);
        $this->assertDatabaseHas('template_pages', [
            'name_translation_key' => 'Contact information',
            'can_be_deleted' => false,
        ]);
        $this->assertDatabaseHas('template_pages', [
            'name_translation_key' => 'Life & goals',
            'can_be_deleted' => true,
        ]);
        $this->assertDatabaseHas('modules', [
            'account_id' => $user->account_id,
            'name_translation_key' => 'Notes',
        ]);
        $this->assertDatabaseHas('modules', [
            'account_id' => $user->account_id,
            'name_translation_key' => 'Contact name',
        ]);
        $this->assertDatabaseHas('modules', [
            'account_id' => $user->account_id,
            'name_translation_key' => 'Avatar',
        ]);
        $this->assertDatabaseHas('modules', [
            'account_id' => $user->account_id,
            'name_translation_key' => 'Contact feed',
        ]);
        $this->assertDatabaseHas('modules', [
            'account_id' => $user->account_id,
            'name_translation_key' => 'Gender and pronoun',
        ]);
        $this->assertDatabaseHas('modules', [
            'account_id' => $user->account_id,
            'name_translation_key' => 'Labels',
        ]);
        $this->assertDatabaseHas('modules', [
            'account_id' => $user->account_id,
            'name_translation_key' => 'Reminders',
        ]);
        $this->assertDatabaseHas('modules', [
            'account_id' => $user->account_id,
            'name_translation_key' => 'Loans',
        ]);
        $this->assertDatabaseHas('modules', [
            'account_id' => $user->account_id,
            'name_translation_key' => 'Job information',
        ]);
        $this->assertDatabaseHas('modules', [
            'account_id' => $user->account_id,
            'name_translation_key' => 'Religions',
        ]);
        $this->assertDatabaseHas('modules', [
            'account_id' => $user->account_id,
            'name_translation_key' => 'Tasks',
        ]);
        $this->assertDatabaseHas('modules', [
            'account_id' => $user->account_id,
            'name_translation_key' => 'Calls',
        ]);
        $this->assertDatabaseHas('modules', [
            'account_id' => $user->account_id,
            'name_translation_key' => 'Pets',
        ]);
        $this->assertDatabaseHas('modules', [
            'account_id' => $user->account_id,
            'name_translation_key' => 'Goals',
        ]);
        $this->assertDatabaseHas('modules', [
            'account_id' => $user->account_id,
            'name_translation_key' => 'Life',
        ]);
        $this->assertDatabaseHas('modules', [
            'account_id' => $user->account_id,
            'name_translation_key' => 'Family summary',
        ]);
        $this->assertDatabaseHas('modules', [
            'account_id' => $user->account_id,
            'name_translation_key' => 'Addresses',
        ]);
        $this->assertDatabaseHas('modules', [
            'account_id' => $user->account_id,
            'name_translation_key' => 'Groups',
        ]);
        $this->assertDatabaseHas('modules', [
            'account_id' => $user->account_id,
            'name_translation_key' => 'Posts',
        ]);

        $this->assertDatabaseHas('relationship_group_types', [
            'name_translation_key' => 'Love',
            'can_be_deleted' => false,
            'type' => RelationshipGroupType::TYPE_LOVE,
        ]);
        $this->assertDatabaseHas('relationship_group_types', [
            'name_translation_key' => 'Family',
            'can_be_deleted' => false,
            'type' => RelationshipGroupType::TYPE_FAMILY,
        ]);
        $this->assertDatabaseHas('relationship_group_types', [
            'name_translation_key' => 'Work',
            'can_be_deleted' => true,
        ]);
        $this->assertDatabaseHas('relationship_group_types', [
            'name_translation_key' => 'Friend',
            'can_be_deleted' => true,
        ]);

        $this->assertDatabaseHas('genders', [
            'account_id' => $user->account_id,
            'name_translation_key' => 'Male',
        ]);
        $this->assertDatabaseHas('genders', [
            'account_id' => $user->account_id,
            'name_translation_key' => 'Other',
        ]);
        $this->assertDatabaseHas('genders', [
            'account_id' => $user->account_id,
            'name_translation_key' => 'Female',
        ]);

        $this->assertDatabaseHas('pronouns', [
            'account_id' => $user->account_id,
            'name_translation_key' => 'he/him',
        ]);
        $this->assertDatabaseHas('pronouns', [
            'account_id' => $user->account_id,
            'name_translation_key' => 'she/her',
        ]);
        $this->assertDatabaseHas('pronouns', [
            'account_id' => $user->account_id,
            'name_translation_key' => 'they/them',
        ]);
        $this->assertDatabaseHas('pronouns', [
            'account_id' => $user->account_id,
            'name_translation_key' => 'per/per',
        ]);
        $this->assertDatabaseHas('pronouns', [
            'account_id' => $user->account_id,
            'name_translation_key' => 've/ver',
        ]);
        $this->assertDatabaseHas('pronouns', [
            'account_id' => $user->account_id,
            'name_translation_key' => 'xe/xem',
        ]);
        $this->assertDatabaseHas('pronouns', [
            'account_id' => $user->account_id,
            'name_translation_key' => 'ze/hir',
        ]);

        $this->assertDatabaseHas('contact_information_types', [
            'account_id' => $user->account_id,
            'name' => trans('account.contact_information_type_email'),
            'protocol' => 'mailto:',
        ]);
        $this->assertDatabaseHas('contact_information_types', [
            'account_id' => $user->account_id,
            'name' => trans('account.contact_information_type_phone'),
            'protocol' => 'tel:',
        ]);
        $this->assertDatabaseHas('contact_information_types', [
            'account_id' => $user->account_id,
            'name' => trans('account.contact_information_type_facebook'),
        ]);
        $this->assertDatabaseHas('contact_information_types', [
            'account_id' => $user->account_id,
            'name' => trans('account.contact_information_type_twitter'),
        ]);
        $this->assertDatabaseHas('contact_information_types', [
            'account_id' => $user->account_id,
            'name' => trans('account.contact_information_type_whatsapp'),
        ]);
        $this->assertDatabaseHas('contact_information_types', [
            'account_id' => $user->account_id,
            'name' => trans('account.contact_information_type_telegram'),
        ]);
        $this->assertDatabaseHas('contact_information_types', [
            'account_id' => $user->account_id,
            'name' => trans('account.contact_information_type_hangouts'),
        ]);
        $this->assertDatabaseHas('contact_information_types', [
            'account_id' => $user->account_id,
            'name' => trans('account.contact_information_type_linkedin'),
        ]);
        $this->assertDatabaseHas('contact_information_types', [
            'account_id' => $user->account_id,
            'name' => trans('account.contact_information_type_instagram'),
        ]);

        $this->assertDatabaseHas('address_types', [
            'account_id' => $user->account_id,
            'name_translation_key' => '🏡 Home',
        ]);

        $this->assertDatabaseHas('pet_categories', [
            'account_id' => $user->account_id,
            'name' => trans('account.pets_dog'),
        ]);
        $this->assertDatabaseHas('pet_categories', [
            'account_id' => $user->account_id,
            'name' => trans('account.pets_cat'),
        ]);
        $this->assertDatabaseHas('pet_categories', [
            'account_id' => $user->account_id,
            'name' => trans('account.pets_bird'),
        ]);
        $this->assertDatabaseHas('pet_categories', [
            'account_id' => $user->account_id,
            'name' => trans('account.pets_fish'),
        ]);
        $this->assertDatabaseHas('pet_categories', [
            'account_id' => $user->account_id,
            'name' => trans('account.pets_hamster'),
        ]);
        $this->assertDatabaseHas('pet_categories', [
            'account_id' => $user->account_id,
            'name' => trans('account.pets_horse'),
        ]);
        $this->assertDatabaseHas('pet_categories', [
            'account_id' => $user->account_id,
            'name' => trans('account.pets_rabbit'),
        ]);
        $this->assertDatabaseHas('pet_categories', [
            'account_id' => $user->account_id,
            'name' => trans('account.pets_rat'),
        ]);
        $this->assertDatabaseHas('pet_categories', [
            'account_id' => $user->account_id,
            'name' => trans('account.pets_reptile'),
        ]);
        $this->assertDatabaseHas('pet_categories', [
            'account_id' => $user->account_id,
            'name' => trans('account.pets_small_animal'),
        ]);
        $this->assertDatabaseHas('pet_categories', [
            'account_id' => $user->account_id,
            'name' => trans('account.pets_other'),
        ]);

        $this->assertDatabaseHas('call_reason_types', [
            'account_id' => $user->account_id,
            'label_translation_key' => 'Personal',
        ]);
        $this->assertDatabaseHas('call_reason_types', [
            'account_id' => $user->account_id,
            'label_translation_key' => 'Business',
        ]);

        $this->assertDatabaseHas('gift_occasions', [
            'label' => 'Birthday',
            'position' => 1,
        ]);
        $this->assertDatabaseHas('gift_occasions', [
            'label' => 'Anniversary',
            'position' => 2,
        ]);
        $this->assertDatabaseHas('gift_occasions', [
            'label' => 'Christmas',
            'position' => 3,
        ]);
        $this->assertDatabaseHas('gift_occasions', [
            'label' => 'Just because',
            'position' => 4,
        ]);
        $this->assertDatabaseHas('gift_occasions', [
            'label' => 'Wedding',
            'position' => 5,
        ]);

        $this->assertDatabaseHas('gift_states', [
            'label' => trans('account.gift_state_idea'),
            'position' => 1,
        ]);
        $this->assertDatabaseHas('gift_states', [
            'label' => trans('account.gift_state_searched'),
            'position' => 2,
        ]);
        $this->assertDatabaseHas('gift_states', [
            'label' => trans('account.gift_state_found'),
            'position' => 3,
        ]);
        $this->assertDatabaseHas('gift_states', [
            'label' => trans('account.gift_state_bought'),
            'position' => 4,
        ]);
        $this->assertDatabaseHas('gift_states', [
            'label' => trans('account.gift_state_offered'),
            'position' => 5,
        ]);

        $this->assertDatabaseHas('post_templates', [
            'label' => trans('settings.personalize_post_templates_default_template'),
            'position' => 1,
        ]);
        $this->assertDatabaseHas('post_templates', [
            'label' => trans('settings.personalize_post_templates_default_template_inspirational'),
            'position' => 2,
        ]);
        $this->assertDatabaseHas('post_template_sections', [
            'label' => trans('settings.personalize_post_templates_default_template_section'),
            'position' => 1,
        ]);
        $this->assertDatabaseHas('post_template_sections', [
            'label' => trans('settings.personalize_post_templates_default_template_section_grateful'),
            'position' => 1,
        ]);
        $this->assertDatabaseHas('post_template_sections', [
            'label' => trans('settings.personalize_post_templates_default_template_section_daily_affirmation'),
            'position' => 2,
        ]);
        $this->assertDatabaseHas('post_template_sections', [
            'label' => trans('settings.personalize_post_templates_default_template_section_better'),
            'position' => 3,
        ]);
        $this->assertDatabaseHas('post_template_sections', [
            'label' => trans('settings.personalize_post_templates_default_template_section_day'),
            'position' => 4,
        ]);
        $this->assertDatabaseHas('post_template_sections', [
            'label' => trans('settings.personalize_post_templates_default_template_section_three_things'),
            'position' => 5,
        ]);

        $this->assertDatabaseHas('religions', [
            'translation_key' => 'account.religion_christianity',
            'position' => 1,
        ]);
        $this->assertDatabaseHas('religions', [
            'translation_key' => 'account.religion_islam',
            'position' => 2,
        ]);
        $this->assertDatabaseHas('religions', [
            'translation_key' => 'account.religion_hinduism',
            'position' => 3,
        ]);
    }
}
