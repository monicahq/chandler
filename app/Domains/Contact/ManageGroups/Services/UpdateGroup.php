<?php

namespace App\Domains\Contact\ManageGroups\Services;

use App\Interfaces\ServiceInterface;
use App\Models\Group;
use App\Services\BaseService;

class UpdateGroup extends BaseService implements ServiceInterface
{
    private Group $group;

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
            'group_id' => 'required|integer|exists:groups,id',
            'group_type_id' => 'required|integer|exists:group_types,id',
            'name' => 'nullable|string|max:255',
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
     * Update a group.
     */
    public function execute(array $data): Group
    {
        $this->data = $data;
        $this->validate();

        $this->group->name = $this->valueOrNull($data, 'name');
        $this->group->group_type_id = $this->valueOrNull($data, 'group_type_id');
        $this->group->save();

        return $this->group;
    }

    private function validate(): void
    {
        $this->validateRules($this->data);

        $this->group = $this->vault->groups()
            ->findOrFail($this->data['group_id']);

        $this->account()->groupTypes()
            ->findOrFail($this->data['group_type_id']);
    }
}
