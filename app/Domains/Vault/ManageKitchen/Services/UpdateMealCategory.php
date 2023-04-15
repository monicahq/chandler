<?php

namespace App\Domains\Vault\ManageKitchen\Services;

use App\Interfaces\ServiceInterface;
use App\Models\MealCategory;
use App\Services\BaseService;

class UpdateMealCategory extends BaseService implements ServiceInterface
{
    private MealCategory $mealCategory;

    private array $data;

    /**
     * Get the validation rules that apply to the service.
     */
    public function rules(): array
    {
        return [
            'account_id' => 'required|uuid|exists:accounts,id',
            'vault_id' => 'required|uuid|exists:vaults,id',
            'author_id' => 'required|uuid|exists:users,id',
            'meal_category_id' => 'required|integer|exists:meal_categories,id',
            'label' => 'required|string|max:255',
        ];
    }

    /**
     * Get the permissions that apply to the user calling the service.
     */
    public function permissions(): array
    {
        return [
            'author_must_belong_to_account',
            'vault_must_belong_to_account',
            'author_must_be_vault_editor',
        ];
    }

    public function execute(array $data): MealCategory
    {
        $this->data = $data;
        $this->validate();

        $this->mealCategory->label = $data['label'];
        $this->mealCategory->save();

        return $this->mealCategory;
    }

    private function validate(): void
    {
        $this->validateRules($this->data);

        $this->mealCategory = $this->vault->mealCategories()
            ->findOrfail($this->data['meal_category_id']);
    }
}
