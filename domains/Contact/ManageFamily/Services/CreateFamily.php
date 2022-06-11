<?php

namespace App\Contact\ManageFamily\Services;

use App\Interfaces\ServiceInterface;
use App\Models\Family;
use App\Services\BaseService;

class CreateFamily extends BaseService implements ServiceInterface
{
    private Family $family;

    /**
     * Get the validation rules that apply to the service.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'account_id' => 'required|integer|exists:accounts,id',
            'vault_id' => 'required|integer|exists:vaults,id',
            'author_id' => 'required|integer|exists:users,id',
            'name' => 'nullable|string|max:255',
        ];
    }

    /**
     * Get the permissions that apply to the user calling the service.
     *
     * @return array
     */
    public function permissions(): array
    {
        return [
            'author_must_belong_to_account',
            'vault_must_belong_to_account',
            'author_must_be_vault_editor',
        ];
    }

    /**
     * Create a family.
     *
     * @param  array  $data
     * @return Family
     */
    public function execute(array $data): Family
    {
        $this->validateRules($data);

        $this->family = Family::create([
            'vault_id' => $data['vault_id'],
            'name' => $this->valueOrNull($data, 'name'),
        ]);

        return $this->family;
    }
}
