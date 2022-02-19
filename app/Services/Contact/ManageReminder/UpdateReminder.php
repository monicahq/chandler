<?php

namespace App\Services\Contact\ManageReminder;

use Carbon\Carbon;
use App\Jobs\CreateAuditLog;
use App\Services\BaseService;
use App\Jobs\CreateContactLog;
use App\Models\ContactReminder;
use App\Interfaces\ServiceInterface;

class UpdateReminder extends BaseService implements ServiceInterface
{
    private ContactReminder $reminder;

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
            'contact_reminder_id' => 'required|integer|exists:contact_reminders,id',
            'name' => 'required|string|max:255',
            'date_to_be_reminded_of' => 'required|date_format:Y-m-d',
            'frequency' => 'required|string:255',
            'frequency_number' => 'nullable|integer',
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
            'contact_must_belong_to_vault',
            'author_must_be_vault_editor',
        ];
    }

    /**
     * Update a reminder.
     *
     * @param  array  $data
     * @return ContactReminder
     */
    public function execute(array $data): ContactReminder
    {
        $this->validateRules($data);

        $this->reminder = ContactReminder::where('contact_id', $data['contact_id'])
            ->findOrFail($data['contact_reminder_id']);

        $this->reminder->name = $data['name'];
        $this->reminder->date_to_be_reminded_of = $data['date_to_be_reminded_of'];
        $this->reminder->frequency = $data['frequency'];
        $this->reminder->frequency_number = $this->valueOrNull($data, 'frequency_number');
        $this->reminder->save();

        $this->contact->last_updated_at = Carbon::now();
        $this->contact->save();

        $this->log();

        return $this->reminder;
    }

    private function log(): void
    {
        CreateAuditLog::dispatch([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'author_name' => $this->author->name,
            'action_name' => 'contact_reminder_updated',
            'objects' => json_encode([
                'contact_id' => $this->contact->id,
                'contact_name' => $this->contact->name,
                'reminder_name' => $this->reminder->name,
            ]),
        ]);

        CreateContactLog::dispatch([
            'contact_id' => $this->contact->id,
            'author_id' => $this->author->id,
            'author_name' => $this->author->name,
            'action_name' => 'contact_reminder_updated',
            'objects' => json_encode([
                'reminder_name' => $this->reminder->name,
            ]),
        ]);
    }
}
