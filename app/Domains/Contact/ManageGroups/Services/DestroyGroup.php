<?php

namespace App\Domains\Contact\ManageGroups\Services;

use App\Interfaces\ServiceInterface;
use App\Services\BaseService;

class DestroyGroup extends BaseService implements ServiceInterface
{
    /**
     * Get the validation rules that apply to the service.
     */
    public function rules(): array
    {
        return [
            'account_id' => 'required|string|exists:accounts,id',
            'vault_id' => 'required|string|exists:vaults,id',
            'author_id' => 'required|string|exists:users,id',
            'group_id' => 'required|integer|exists:groups,id',
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

    /**
     * Destroy a group.
     */
    public function execute(array $data): void
    {
        $this->validateRules($data);

        $group = $this->vault->groups()
            ->findOrFail($data['group_id']);

        $group->delete();
    }
}
