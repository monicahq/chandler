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
            'label_translation_key' => trans_key('Breakfast'),
            'position' => 1,
        ]);
        MealCategory::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Lunch'),
            'position' => 2,
        ]);
        MealCategory::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Dinner'),
            'position' => 3,
        ]);
        MealCategory::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Snack'),
            'position' => 4,
        ]);
        MealCategory::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Drink'),
            'position' => 5,
        ]);
        MealCategory::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Dessert'),
            'position' => 6,
        ]);
    }

    private function populateDefaultIngredients(): void
    {
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Tomatoes'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Onions'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Garlic'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Carrots'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Bell peppers'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Spinach'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Broccoli'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Cauliflower'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Potatoes'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Sweet potatoes'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Peas'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Corn'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Green beans'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Asparagus'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Zucchini'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Eggplant'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Cabbage'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Lettuce'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Kale'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Collard greens'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Brussels sprouts'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Artichokes'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Beets'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Radishes'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Turnips'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Parsnips'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Squash'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Pumpkins'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Okra'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Cucumbers'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Mushrooms'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Avocado'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Chicken'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Beef'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Pork'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Lamb'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Veal'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Chicken'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Turkey'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Duck'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Goose'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Quail'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Pheasant'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Venison'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Bison'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Rabbit'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Goat'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Horse'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Kangaroo'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Ostrich'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Alligator'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Salmon'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Tuna'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Cod'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Haddock'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Halibut'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Tilapia'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Trout'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Catfish'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Snapper'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Grouper'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Mahi mahi'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Swordfish'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Mackerel'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Sardines'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Anchovies'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Sea bass'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Sole'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Flounder'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Perch'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Pike'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Rainbow trout'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Arctic char'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Carp'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Eel'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Barramundi'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Redfish'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Wahoo'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Whitefish'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Herring'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Monkfish'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Tilefish'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Yellowfin tuna'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Bluefin tuna'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Shark'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Cobia'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Pompano'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Rockfish'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Black cod'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Red snapper'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Amberjack'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Shrimp'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Lobster'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Crab'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Clams'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Mussels'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Oysters'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Tofu'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Tempeh'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Seitan'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Eggs'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Beans'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Lentils'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Chickpeas'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Peanuts'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Almonds'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Walnuts'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Cashews'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Pistachios'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Pumpkin seeds'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Sunflower seeds'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Rice'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Quinoa'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Barley'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Oats'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Wheat'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Rye'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Cornmeal'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Bulgur'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Couscous'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Bread'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Pasta'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Noodles'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Salt'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Pepper'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Oregano'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Basil'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Thyme'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Rosemary'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Sage'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Cilantro'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Parsley'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Dill'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Bay leaves'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Curry powder'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Cumin'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Coriander'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Paprika'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Chili powder'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Garlic powder'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Onion powder'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Turmeric'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Ginger'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Nutmeg'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Cinnamon'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Cloves'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Cardamom'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Mustard seeds'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Olive oil'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Vegetable oil'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Canola oil'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Coconut oil'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Butter'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Margarine'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Lard'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Shortening'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Ghee'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Milk'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Cheese'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Yogurt'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Sour cream'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Butter'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Cream'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Cream cheese'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Apples'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Bananas'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Oranges'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Grapes'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Berries'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Pineapple'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Mango'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Papaya'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Kiwi'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Peaches'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Plums'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Pears'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Cherries'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Grapefruit'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Lemons'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Limes'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Melons'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Sugar'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Brown sugar'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Honey'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Maple syrup'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Agave nectar'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Stevia'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Molasses'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Ketchup'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Mustard'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Mayonnaise'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Barbecue sauce'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Soy sauce'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Hoisin sauce'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Teriyaki sauce'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Worcestershire sauce'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Hot sauce'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Salsa'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Guacamole'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Salad dressing'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Vinegar'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Relish'),
        ]);
        Ingredient::create([
            'vault_id' => $this->vault->id,
            'label_translation_key' => trans_key('Pickles'),
        ]);
    }
}
