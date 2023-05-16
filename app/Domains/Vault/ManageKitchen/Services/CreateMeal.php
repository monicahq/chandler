<?php

namespace App\Domains\Vault\ManageKitchen\Services;

use App\Interfaces\ServiceInterface;
use App\Models\Meal;
use App\Services\BaseService;

class CreateMeal extends BaseService implements ServiceInterface
{
    /**
     * Get the validation rules that apply to the service.
     */
    public function rules(): array
    {
        return [
            'account_id' => 'required|uuid|exists:accounts,id',
            'vault_id' => 'required|uuid|exists:vaults,id',
            'author_id' => 'required|uuid|exists:users,id',
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
        $this->validateRules($data);

        return Meal::create([
            'vault_id' => $data['vault_id'],
            'name' => $data['name'],
            'url_to_recipe' => $this->valueOrNull($data, 'url_to_recipe'),
            'time_to_prepare_in_minutes' => $this->valueOrNull($data, 'time_to_prepare_in_minutes'),
            'time_to_cook_in_minutes' => $this->valueOrNull($data, 'time_to_cook_in_minutes'),
        ]);
    }
}
