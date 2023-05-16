<?php

namespace App\Domains\Vault\ManageKitchen\Services;

use App\Interfaces\ServiceInterface;
use App\Models\Meal;
use App\Services\BaseService;

class UpdateMeal extends BaseService implements ServiceInterface
{
    private Meal $meal;

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
            'meal_id' => 'required|integer|exists:meals,id',
            'name' => 'required|string|max:255',
            'url_to_recipe' => 'nullable|string|max:255',
            'time_to_prepare_in_minutes' => 'nullable|integer',
            'time_to_cook_in_minutes' => 'nullable|integer',
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

    public function execute(array $data): Meal
    {
        $this->data = $data;
        $this->validate();

        $this->meal->name = $data['name'];
        $this->meal->url_to_recipe = $this->valueOrNull($data, 'url_to_recipe');
        $this->meal->time_to_prepare_in_minutes = $this->valueOrNull($data, 'time_to_prepare_in_minutes');
        $this->meal->time_to_cook_in_minutes = $this->valueOrNull($data, 'time_to_cook_in_minutes');
        $this->meal->save();

        return $this->meal;
    }

    private function validate(): void
    {
        $this->validateRules($this->data);

        $this->meal = $this->vault->meals()->findOrfail($this->data['meal_id']);
    }
}
