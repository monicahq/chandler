<?php

namespace App\Domains\Vault\ManageKitchen\Services;

use App\Interfaces\ServiceInterface;
use App\Models\Ingredient;
use App\Services\BaseService;

class CreateIngredient extends BaseService implements ServiceInterface
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

    public function execute(array $data): Ingredient
    {
        $this->validateRules($data);

        return Ingredient::create([
            'vault_id' => $data['vault_id'],
            'label' => $data['label'],
        ]);
    }
}
