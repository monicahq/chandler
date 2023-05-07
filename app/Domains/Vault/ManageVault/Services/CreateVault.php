<?php

namespace App\Domains\Vault\ManageVault\Services;

use App\Domains\Vault\ManageVaultImportantDateTypes\Services\CreateContactImportantDateType;
use App\Interfaces\ServiceInterface;
use App\Models\Contact;
use App\Models\ContactImportantDate;
use App\Models\Ingredient;
use App\Models\LifeEventCategory;
use App\Models\LifeEventType;
use App\Models\MealCategory;
use App\Models\MoodTrackingParameter;
use App\Models\Vault;
use App\Models\VaultQuickFactsTemplate;
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
        $this->populateDefaultIngredients();

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
            'label' => trans('Birthdate'),
            'internal_type' => ContactImportantDate::TYPE_BIRTHDATE,
            'can_be_deleted' => false,
        ]);

        (new CreateContactImportantDateType())->execute([
            'account_id' => $this->data['account_id'],
            'author_id' => $this->author->id,
            'vault_id' => $this->vault->id,
            'label' => trans('Deceased date'),
            'internal_type' => ContactImportantDate::TYPE_DECEASED_DATE,
            'can_be_deleted' => false,
        ]);
    }

    private function populateMoodTrackingParameters(): void
    {
        MoodTrackingParameter::create([
            'vault_id' => $this->vault->id,
            'label' => null,
            'label_translation_key' => trans_key('🥳 Awesome'),
            'position' => 1,
            'hex_color' => 'bg-lime-500',
        ]);
        MoodTrackingParameter::create([
            'vault_id' => $this->vault->id,
            'label' => null,
            'label_translation_key' => trans_key('😀 Good'),
            'position' => 2,
            'hex_color' => 'bg-lime-300',
        ]);
        MoodTrackingParameter::create([
            'vault_id' => $this->vault->id,
            'label' => null,
            'label_translation_key' => trans_key('😐 Meh'),
            'position' => 3,
            'hex_color' => 'bg-cyan-600',
        ]);
        MoodTrackingParameter::create([
            'vault_id' => $this->vault->id,
            'label' => null,
            'label_translation_key' => trans_key('😔 Bad'),
            'position' => 4,
            'hex_color' => 'bg-orange-300',
        ]);
        MoodTrackingParameter::create([
            'vault_id' => $this->vault->id,
            'label' => null,
            'label_translation_key' => trans_key('😩 Awful'),
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
            'label_translation_key' => trans_key('Transportation'),
            'can_be_deleted' => true,
        ]);

        LifeEventType::create([
            'life_event_category_id' => $category->id,
            'position' => 1,
            'label' => null,
            'label_translation_key' => trans_key('Rode a bike'),
            'can_be_deleted' => true,
        ]);
        LifeEventType::create([
            'life_event_category_id' => $category->id,
            'position' => 2,
            'label' => null,
            'label_translation_key' => trans_key('Drove'),
            'can_be_deleted' => true,
        ]);
        LifeEventType::create([
            'life_event_category_id' => $category->id,
            'position' => 3,
            'label' => null,
            'label_translation_key' => trans_key('Walked'),
            'can_be_deleted' => true,
        ]);
        LifeEventType::create([
            'life_event_category_id' => $category->id,
            'position' => 4,
            'label' => null,
            'label_translation_key' => trans_key('Took the bus'),
            'can_be_deleted' => true,
        ]);
        LifeEventType::create([
            'life_event_category_id' => $category->id,
            'position' => 5,
            'label' => null,
            'label_translation_key' => trans_key('Took the metro'),
            'can_be_deleted' => true,
        ]);

        // social category
        $category = LifeEventCategory::create([
            'vault_id' => $this->vault->id,
            'position' => 2,
            'label' => null,
            'label_translation_key' => trans_key('Social'),
            'can_be_deleted' => true,
        ]);
        LifeEventType::create([
            'life_event_category_id' => $category->id,
            'position' => 1,
            'label' => null,
            'label_translation_key' => trans_key('Ate'),
            'can_be_deleted' => true,
        ]);
        LifeEventType::create([
            'life_event_category_id' => $category->id,
            'position' => 2,
            'label' => null,
            'label_translation_key' => trans_key('Drank'),
            'can_be_deleted' => true,
        ]);
        LifeEventType::create([
            'life_event_category_id' => $category->id,
            'position' => 3,
            'label' => null,
            'label_translation_key' => trans_key('Went to a bar'),
            'can_be_deleted' => true,
        ]);
        LifeEventType::create([
            'life_event_category_id' => $category->id,
            'position' => 4,
            'label' => null,
            'label_translation_key' => trans_key('Watched a movie'),
            'can_be_deleted' => true,
        ]);
        LifeEventType::create([
            'life_event_category_id' => $category->id,
            'position' => 5,
            'label' => null,
            'label_translation_key' => trans_key('Watched TV'),
            'can_be_deleted' => true,
        ]);
        LifeEventType::create([
            'life_event_category_id' => $category->id,
            'position' => 6,
            'label' => null,
            'label_translation_key' => trans_key('Watched a tv show'),
            'can_be_deleted' => true,
        ]);

        // sport category
        $category = LifeEventCategory::create([
            'vault_id' => $this->vault->id,
            'position' => 3,
            'label' => null,
            'label_translation_key' => trans_key('Sport'),
            'can_be_deleted' => true,
        ]);
        LifeEventType::create([
            'life_event_category_id' => $category->id,
            'position' => 1,
            'label' => null,
            'label_translation_key' => trans_key('Ran'),
            'can_be_deleted' => true,
        ]);
        LifeEventType::create([
            'life_event_category_id' => $category->id,
            'position' => 2,
            'label' => null,
            'label_translation_key' => trans_key('Played soccer'),
            'can_be_deleted' => true,
        ]);
        LifeEventType::create([
            'life_event_category_id' => $category->id,
            'position' => 3,
            'label' => null,
            'label_translation_key' => trans_key('Played basketball'),
            'can_be_deleted' => true,
        ]);
        LifeEventType::create([
            'life_event_category_id' => $category->id,
            'position' => 4,
            'label' => null,
            'label_translation_key' => trans_key('Played golf'),
            'can_be_deleted' => true,
        ]);
        LifeEventType::create([
            'life_event_category_id' => $category->id,
            'position' => 5,
            'label' => null,
            'label_translation_key' => trans_key('Played tennis'),
            'can_be_deleted' => true,
        ]);

        // work category
        $category = LifeEventCategory::create([
            'vault_id' => $this->vault->id,
            'position' => 4,
            'label' => null,
            'label_translation_key' => trans_key('Work'),
            'can_be_deleted' => true,
        ]);
        LifeEventType::create([
            'life_event_category_id' => $category->id,
            'position' => 1,
            'label' => null,
            'label_translation_key' => trans_key('Took a new job'),
            'can_be_deleted' => true,
        ]);
        LifeEventType::create([
            'life_event_category_id' => $category->id,
            'position' => 2,
            'label' => null,
            'label_translation_key' => trans_key('Quit job'),
            'can_be_deleted' => true,
        ]);
        LifeEventType::create([
            'life_event_category_id' => $category->id,
            'position' => 3,
            'label' => null,
            'label_translation_key' => trans_key('Got fired'),
            'can_be_deleted' => true,
        ]);
        LifeEventType::create([
            'life_event_category_id' => $category->id,
            'position' => 4,
            'label' => null,
            'label_translation_key' => trans_key('Had a promotion'),
            'can_be_deleted' => true,
        ]);
    }

    private function populateDefaultQuickVaultTemplateEntries(): void
    {
        VaultQuickFactsTemplate::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Hobbies'),
            'position' => 1,
        ]);

        VaultQuickFactsTemplate::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Food preferences'),
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

    private function populateDefaultIngredients(): void
    {
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_tomatoes',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_onions',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_garlic',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_carrots',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_bell_peppers',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_spinach',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_broccoli',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_cauliflower',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_potatoes',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_sweet_potatoes',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_peas',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_corn',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_green_beans',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_asparagus',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_zucchini',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_eggplant',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_cabbage',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_lettuce',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_kale',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_collard_greens',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_brussels_sprouts',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_artichokes',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_beets',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_radishes',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_turnips',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_parsnips',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_squash',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_pumpkins',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_okra',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_cucumbers',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_mushrooms',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_avocado',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_chicken',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_beef',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_pork',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_lamb',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_veal',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_chicken',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_turkey',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_duck',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_goose',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_quail',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_pheasant',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_venison',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_bison',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_rabbit',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_goat',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_horse',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_kangaroo',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_ostrich',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_alligator',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_salmon',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_tuna',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_cod',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_haddock',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_halibut',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_tilapia',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_trout',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_catfish',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_snapper',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_grouper',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_mahi mahi',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_swordfish',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_mackerel',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_sardines',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_anchovies',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_sea bass',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_sole',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_flounder',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_perch',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_pike',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_rainbow_trout',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_arctic_char',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_carp',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_eel',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_barramundi',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_redfish',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_wahoo',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_whitefish',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_herring',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_monkfish',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_tilefish',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_yellowfin_tuna',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_bluefin_tuna',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_shark',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_cobia',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_pompano',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_rockfish',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_black_cod',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_red_snapper',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_amberjack',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_shrimp',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_lobster',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_crab',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_clams',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_mussels',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_oysters',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_tofu',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_tempeh',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_seitan',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_eggs',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_beans',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_lentils',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_chickpeas',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_peanuts',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_almonds',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_walnuts',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_cashews',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_pistachios',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_pumpkin_seeds',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_sunflower_seeds',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_rice',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_quinoa',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_barley',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_oats',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_wheat',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_rye',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_cornmeal',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_bulgur',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_couscous',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_bread',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_pasta',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_noodles',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_salt',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_pepper',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_oregano',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_basil',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_thyme',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_rosemary',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_sage',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_cilantro',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_parsley',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_dill',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_bay_leaves',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_curry_powder',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_cumin',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_coriander',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_paprika',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_chili_powder',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_garlic_powder',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_onion_powder',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_turmeric',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_ginger',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_nutmeg',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_cinnamon',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_cloves',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_cardamom',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_mustard_seeds',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_olive_oil',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_vegetable_oil',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_canola_oil',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_coconut_oil',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_butter',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_margarine',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_lard',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_shortening',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_ghee',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_milk',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_cheese',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_yogurt',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_sour_cream',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_butter',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_cream',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_cream_cheese',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_apples',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_bananas',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_oranges',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_grapes',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_berries',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_pineapple',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_mango',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_papaya',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_kiwi',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_peaches',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_plums',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_pears',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_cherries',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_grapefruit',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_lemons',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_limes',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_melons',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_sugar',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_brown_sugar',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_honey',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_maple_syrup',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_agave_nectar',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_stevia',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_molasses',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_ketchup',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_mustard',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_mayonnaise',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_barbecue_sauce',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_soy_sauce',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_hoisin_sauce',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_teriyaki_sauce',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_worcestershire_sauce',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_hot_sauce',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_salsa',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_guacamole',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_salad_dressing',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_vinegar',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_relish',
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => 'vault.meal_ingredients_pickles',
        ]);
    }
}
