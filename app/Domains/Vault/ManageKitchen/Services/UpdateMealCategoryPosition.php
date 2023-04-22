<?php

namespace App\Domains\Vault\ManageKitchen\Services;

use App\Interfaces\ServiceInterface;
use App\Models\MealCategory;
use App\Services\BaseService;

class UpdateMealCategoryPosition extends BaseService implements ServiceInterface
{
    private MealCategory $mealCategory;

    private int $pastPosition;

    private array $data;

    /**
     * Get the validation rules that apply to the service.
     */
    public function rules(): array
    {
        return [
            'account_id' => 'required|uuid|exists:accounts,id',
            'author_id' => 'required|uuid|exists:users,id',
            'vault_id' => 'required|uuid|exists:vaults,id',
            'meal_category_id' => 'required|integer|exists:meal_categories,id',
            'new_position' => 'required|integer',
        ];
    }

    /**
     * Get the permissions that apply to the user calling the service.
     */
    public function permissions(): array
    {
        return [
            'author_must_belong_to_account',
            'author_must_be_vault_editor',
            'vault_must_belong_to_account',
        ];
    }

    /**
     * Update the meal category's position.
     */
    public function execute(array $data): MealCategory
    {
        $this->data = $data;
        $this->validate();
        $this->updatePosition();

        return $this->mealCategory;
    }

    private function validate(): void
    {
        $this->validateRules($this->data);

        $this->mealCategory = $this->vault->mealCategories()
            ->findOrFail($this->data['meal_category_id']);

        $this->pastPosition = $this->mealCategory->position;
    }

    private function updatePosition(): void
    {
        if ($this->data['new_position'] > $this->pastPosition) {
            $this->updateAscendingPosition();
        } else {
            $this->updateDescendingPosition();
        }

        $this->mealCategory
            ->update([
                'position' => $this->data['new_position'],
            ]);
    }

    private function updateAscendingPosition(): void
    {
        $this->vault->mealCategories()
            ->where('position', '>', $this->pastPosition)
            ->where('position', '<=', $this->data['new_position'])
            ->decrement('position');
    }

    private function updateDescendingPosition(): void
    {
        $this->vault->mealCategories()
            ->where('position', '>=', $this->data['new_position'])
            ->where('position', '<', $this->pastPosition)
            ->increment('position');
    }
}
