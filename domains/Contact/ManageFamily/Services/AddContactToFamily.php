<?php

namespace App\Contact\ManageFamily\Services;

use App\Interfaces\ServiceInterface;
use App\Models\Contact;
use App\Models\Family;
use App\Services\BaseService;

class AddContactToFamily extends BaseService implements ServiceInterface
{
    private Family $family;
    private array $data;

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
            'family_id' => 'required|integer|exists:families,id',
            'contact_id' => 'required|integer|exists:contacts,id',
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
            'contact_must_belong_to_vault',
        ];
    }

    /**
     * Add a contact to a family.
     *
     * @param  array  $data
     * @return Family
     */
    public function execute(array $data): Family
    {
        $this->data = $data;
        $this->validate();

        $this->family->contacts()->syncWithoutDetaching($this->contact->id);

        return $this->family;
    }

    private function validate(): void
    {
        $this->validateRules($this->data);

        $this->family = Family::where('vault_id', $this->data['vault_id'])
            ->findOrFail($this->data['family_id']);
    }
}
