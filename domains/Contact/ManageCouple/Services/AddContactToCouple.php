<?php

namespace App\Contact\ManageCouple\Services;

use App\Models\Couple;
use App\Models\Contact;
use App\Jobs\CreateAuditLog;
use App\Services\BaseService;
use App\Jobs\CreateContactLog;
use App\Interfaces\ServiceInterface;

class AddContactToCouple extends BaseService implements ServiceInterface
{
    private Couple $couple;
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
            'couple_id' => 'required|integer|exists:couples,id',
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
     * Add a contact to a couple.
     *
     * @param  array  $data
     * @return Couple
     */
    public function execute(array $data): Couple
    {
        $this->data = $data;
        $this->validate();

        $this->couple->contacts()->syncWithoutDetaching($this->contact->id);

        return $this->couple;
    }

    private function validate(): void
    {
        $this->validateRules($data);

        $this->couple = Couple::where('vault_id', $this->data['vault_id'])
            ->findOrFail($this->data['couple_id']);
    }
}
