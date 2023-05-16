<?php

namespace App\Domains\Vault\ManageKitchen\Services;

use App\Interfaces\ServiceInterface;
use App\Models\Ingredient;
use App\Services\BaseService;

class UpdateIngredient extends BaseService implements ServiceInterface
{
    private Ingredient $ingredient;

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
            'ingredient_id' => 'required|integer|exists:ingredients,id',
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
        $this->data = $data;
        $this->validate();

        $this->ingredient->label = $data['label'];
        $this->ingredient->save();

        return $this->ingredient;
    }

    private function validate(): void
    {
        $this->validateRules($this->data);

        $this->ingredient = $this->vault->ingredients()->findOrfail($this->data['ingredient_id']);
    }
}
