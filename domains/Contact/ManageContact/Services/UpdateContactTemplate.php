<?php

namespace App\Contact\ManageContact\Services;

use App\Interfaces\ServiceInterface;
use App\Jobs\CreateAuditLog;
use App\Jobs\CreateContactLog;
use App\Models\Contact;
use App\Models\Template;
use App\Services\BaseService;
use Carbon\Carbon;

class UpdateContactTemplate extends BaseService implements ServiceInterface
{
    private array $data;
    private Template $template;

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
            'contact_id' => 'required|integer|exists:contacts,id',
            'template_id' => 'required|integer|exists:templates,id',
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
     * Update the contact's template.
     *
     * @param  array  $data
     * @return Contact
     */
    public function execute(array $data): Contact
    {
        $this->data = $data;

        $this->validate();
        $this->update();
        $this->updateLastEditedDate();
        $this->log();

        return $this->contact;
    }

    private function validate(): void
    {
        $this->validateRules($this->data);

        Template::where('account_id', $this->data['account_id'])
            ->findOrFail($this->data['template_id']);
    }

    private function update(): void
    {
        $this->contact->template_id = $this->data['template_id'];
        $this->contact->last_updated_at = Carbon::now();
        $this->contact->save();
    }

    private function updateLastEditedDate(): void
    {
        $this->contact->last_updated_at = Carbon::now();
        $this->contact->save();
    }

    private function log(): void
    {
        CreateAuditLog::dispatch([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'author_name' => $this->author->name,
            'action_name' => 'contact_template_updated',
            'objects' => json_encode([
                'contact_id' => $this->contact->id,
                'contact_name' => $this->contact->name,
            ]),
        ])->onQueue('low');

        CreateContactLog::dispatch([
            'contact_id' => $this->contact->id,
            'author_id' => $this->author->id,
            'author_name' => $this->author->name,
            'action_name' => 'contact_template_updated',
            'objects' => json_encode([]),
        ])->onQueue('low');
    }
}
