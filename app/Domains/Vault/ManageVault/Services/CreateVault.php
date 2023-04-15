<?php

namespace App\Domains\Vault\ManageVault\Services;

use App\Domains\Vault\ManageVaultImportantDateTypes\Services\CreateContactImportantDateType;
use App\Interfaces\ServiceInterface;
use App\Models\Contact;
use App\Models\ContactImportantDate;
use App\Models\LifeEventCategory;
use App\Models\LifeEventType;
use App\Models\MealCategory;
use App\Models\MoodTrackingParameter;
use App\Models\Vault;
use App\Models\VaultQuickFactTemplate;
use App\Services\BaseService;

class CreateVault extends BaseService implements ServiceInterface
{
    public Vault $vault;

    private array $data;

    /**
     * Get the validation rules that apply to the service.
     */
    public function rules(): array
    {
        return [
            'account_id' => 'required|uuid|exists:accounts,id',
            'author_id' => 'required|uuid|exists:users,id',
            'template_id' => 'nullable|integer|exists:templates,id',
            'type' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ];
    }

    /**
     * Get the permissions that apply to the user calling the service.
     */
    public function permissions(): array
    {
        return [
            'author_must_belong_to_account',
        ];
    }

    /**
     * Create a vault.
     */
    public function execute(array $data): Vault
    {
        $this->validateRules($data);
        $this->data = $data;

        $this->createVault();
        $this->createUserContact();
        $this->populateDefaultContactImportantDateTypes();
        $this->populateMoodTrackingParameters();
        $this->populateDefaultLifeEventCategories();
        $this->populateDefaultQuickVaultTemplateEntries();
        $this->populateDefaultMealCategories();

        return $this->vault;
    }

    private function createVault(): void
    {
        // the vault default's template should be the first template in the
        // account, if it exists
        $template = $this->account()->templates()->first();

        $this->vault = Vault::create([
            'account_id' => $this->data['account_id'],
            'type' => $this->data['type'],
            'name' => $this->data['name'],
            'description' => $this->valueOrNull($this->data, 'description'),
            'default_template_id' => $template ? $template->id : null,
        ]);
    }

    private function createUserContact(): void
    {
        $contact = Contact::create([
            'vault_id' => $this->vault->id,
            'first_name' => $this->author->first_name,
            'last_name' => $this->author->last_name,
            'can_be_deleted' => false,
            'template_id' => $this->vault->default_template_id,
        ]);

        $this->vault->users()->save($this->author, [
            'permission' => Vault::PERMISSION_MANAGE,
            'contact_id' => $contact->id,
        ]);
    }

    private function populateDefaultContactImportantDateTypes(): void
    {
        (new CreateContactImportantDateType())->execute([
            'account_id' => $this->data['account_id'],
            'author_id' => $this->author->id,
            'vault_id' => $this->vault->id,
            'label' => trans('account.vault_contact_important_date_type_internal_type_birthdate'),
            'internal_type' => ContactImportantDate::TYPE_BIRTHDATE,
            'can_be_deleted' => false,
        ]);

        (new CreateContactImportantDateType())->execute([
            'account_id' => $this->data['account_id'],
            'author_id' => $this->author->id,
            'vault_id' => $this->vault->id,
            'label' => trans('account.vault_contact_important_date_type_internal_type_deceased_date'),
            'internal_type' => ContactImportantDate::TYPE_DECEASED_DATE,
            'can_be_deleted' => false,
        ]);
    }

    private function populateMoodTrackingParameters(): void
    {
        MoodTrackingParameter::create([
            'vault_id' => $this->vault->id,
            'label' => null,
            'label_translation_key' => 'vault.settings_mood_tracking_parameters_awesome',
            'position' => 1,
            'hex_color' => 'bg-lime-500',
        ]);
        MoodTrackingParameter::create([
            'vault_id' => $this->vault->id,
            'label' => null,
            'label_translation_key' => 'vault.settings_mood_tracking_parameters_good',
            'position' => 2,
            'hex_color' => 'bg-lime-300',
        ]);
        MoodTrackingParameter::create([
            'vault_id' => $this->vault->id,
            'label' => null,
            'label_translation_key' => 'vault.settings_mood_tracking_parameters_meh',
            'position' => 3,
            'hex_color' => 'bg-cyan-600',
        ]);
        MoodTrackingParameter::create([
            'vault_id' => $this->vault->id,
            'label' => null,
            'label_translation_key' => 'vault.settings_mood_tracking_parameters_bad',
            'position' => 4,
            'hex_color' => 'bg-orange-300',
        ]);
        MoodTrackingParameter::create([
            'vault_id' => $this->vault->id,
            'label' => null,
            'label_translation_key' => 'vault.settings_mood_tracking_parameters_awful',
            'position' => 5,
            'hex_color' => 'bg-red-700',
        ]);
    }

    private function populateDefaultLifeEventCategories(): void
    {
        // transportation category
        $category = LifeEventCategory::create([
            'vault_id' => $this->vault->id,
            'position' => 1,
            'label' => null,
            'label_translation_key' => 'vault.settings_life_event_category_transportation',
            'can_be_deleted' => true,
        ]);

        LifeEventType::create([
            'life_event_category_id' => $category->id,
            'position' => 1,
            'label' => null,
            'label_translation_key' => 'vault.settings_life_event_type_transportation_bike',
            'can_be_deleted' => true,
        ]);
        LifeEventType::create([
            'life_event_category_id' => $category->id,
            'position' => 2,
            'label' => null,
            'label_translation_key' => 'vault.settings_life_event_type_transportation_car',
            'can_be_deleted' => true,
        ]);
        LifeEventType::create([
            'life_event_category_id' => $category->id,
            'position' => 3,
            'label' => null,
            'label_translation_key' => 'vault.settings_life_event_type_transportation_walk',
            'can_be_deleted' => true,
        ]);
        LifeEventType::create([
            'life_event_category_id' => $category->id,
            'position' => 4,
            'label' => null,
            'label_translation_key' => 'vault.settings_life_event_type_transportation_bus',
            'can_be_deleted' => true,
        ]);
        LifeEventType::create([
            'life_event_category_id' => $category->id,
            'position' => 5,
            'label' => null,
            'label_translation_key' => 'vault.settings_life_event_type_transportation_metro',
            'can_be_deleted' => true,
        ]);

        // social category
        $category = LifeEventCategory::create([
            'vault_id' => $this->vault->id,
            'position' => 2,
            'label' => null,
            'label_translation_key' => 'vault.settings_life_event_category_social',
            'can_be_deleted' => true,
        ]);
        LifeEventType::create([
            'life_event_category_id' => $category->id,
            'position' => 1,
            'label' => null,
            'label_translation_key' => 'vault.settings_life_event_type_social_ate',
            'can_be_deleted' => true,
        ]);
        LifeEventType::create([
            'life_event_category_id' => $category->id,
            'position' => 2,
            'label' => null,
            'label_translation_key' => 'vault.settings_life_event_type_social_drank',
            'can_be_deleted' => true,
        ]);
        LifeEventType::create([
            'life_event_category_id' => $category->id,
            'position' => 3,
            'label' => null,
            'label_translation_key' => 'vault.settings_life_event_type_social_bar',
            'can_be_deleted' => true,
        ]);
        LifeEventType::create([
            'life_event_category_id' => $category->id,
            'position' => 4,
            'label' => null,
            'label_translation_key' => 'vault.settings_life_event_type_social_movie',
            'can_be_deleted' => true,
        ]);
        LifeEventType::create([
            'life_event_category_id' => $category->id,
            'position' => 5,
            'label' => null,
            'label_translation_key' => 'vault.settings_life_event_type_social_tv',
            'can_be_deleted' => true,
        ]);
        LifeEventType::create([
            'life_event_category_id' => $category->id,
            'position' => 6,
            'label' => null,
            'label_translation_key' => 'vault.settings_life_event_type_social_tv_show',
            'can_be_deleted' => true,
        ]);

        // sport category
        $category = LifeEventCategory::create([
            'vault_id' => $this->vault->id,
            'position' => 3,
            'label' => null,
            'label_translation_key' => 'vault.settings_life_event_category_sport',
            'can_be_deleted' => true,
        ]);
        LifeEventType::create([
            'life_event_category_id' => $category->id,
            'position' => 1,
            'label' => null,
            'label_translation_key' => 'vault.settings_life_event_type_sport_ran',
            'can_be_deleted' => true,
        ]);
        LifeEventType::create([
            'life_event_category_id' => $category->id,
            'position' => 2,
            'label' => null,
            'label_translation_key' => 'vault.settings_life_event_type_sport_soccer',
            'can_be_deleted' => true,
        ]);
        LifeEventType::create([
            'life_event_category_id' => $category->id,
            'position' => 3,
            'label' => null,
            'label_translation_key' => 'vault.settings_life_event_type_sport_basketball',
            'can_be_deleted' => true,
        ]);
        LifeEventType::create([
            'life_event_category_id' => $category->id,
            'position' => 4,
            'label' => null,
            'label_translation_key' => 'vault.settings_life_event_type_sport_golf',
            'can_be_deleted' => true,
        ]);
        LifeEventType::create([
            'life_event_category_id' => $category->id,
            'position' => 5,
            'label' => null,
            'label_translation_key' => 'vault.settings_life_event_type_sport_tennis',
            'can_be_deleted' => true,
        ]);

        // work category
        $category = LifeEventCategory::create([
            'vault_id' => $this->vault->id,
            'position' => 4,
            'label' => null,
            'label_translation_key' => 'vault.settings_life_event_category_work',
            'can_be_deleted' => true,
        ]);
        LifeEventType::create([
            'life_event_category_id' => $category->id,
            'position' => 1,
            'label' => null,
            'label_translation_key' => 'vault.settings_life_event_type_work_job',
            'can_be_deleted' => true,
        ]);
        LifeEventType::create([
            'life_event_category_id' => $category->id,
            'position' => 2,
            'label' => null,
            'label_translation_key' => 'vault.settings_life_event_type_work_quit',
            'can_be_deleted' => true,
        ]);
        LifeEventType::create([
            'life_event_category_id' => $category->id,
            'position' => 3,
            'label' => null,
            'label_translation_key' => 'vault.settings_life_event_type_work_fired',
            'can_be_deleted' => true,
        ]);
        LifeEventType::create([
            'life_event_category_id' => $category->id,
            'position' => 4,
            'label' => null,
            'label_translation_key' => 'vault.settings_life_event_type_sport_promotion',
            'can_be_deleted' => true,
        ]);
    }

    private function populateDefaultQuickVaultTemplateEntries(): void
    {
        VaultQuickFactTemplate::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.settings_quick_fact_template_entry_hobbies',
            'position' => 1,
        ]);

        VaultQuickFactTemplate::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.settings_quick_fact_template_entry_food',
            'position' => 2,
        ]);
    }

    private function populateDefaultMealCategories(): void
    {
        MealCategory::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_category_breakfast',
            'position' => 1,
        ]);
        MealCategory::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_category_lunch',
            'position' => 2,
        ]);
        MealCategory::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_category_dinner',
            'position' => 3,
        ]);
        MealCategory::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_category_snack',
            'position' => 4,
        ]);
        MealCategory::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_category_drink',
            'position' => 5,
        ]);
        MealCategory::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_category_dessert',
            'position' => 6,
        ]);
    }
}
