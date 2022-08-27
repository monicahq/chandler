<?php

namespace App\Contact\ManageReminders\Services;

use App\Interfaces\ServiceInterface;
use App\Models\ContactReminder;
use App\Services\BaseService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UpdateContactReminder extends BaseService implements ServiceInterface
{
    private ContactReminder $reminder;

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
            'contact_id' => 'required|integer|exists:contacts,id',
            'contact_reminder_id' => 'required|integer|exists:contact_reminders,id',
            'label' => 'required|string|max:255',
            'day' => 'nullable|integer',
            'month' => 'nullable|integer',
            'year' => 'nullable|integer',
            'type' => 'required|string:255',
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
        $this->data = $data;
        $this->validate();

        $this->update();
        $this->deleteOldScheduledReminders();
        $this->updateLastUpdatedDate();
        $this->scheduledReminderForAllUsersInVault();

        return $this->reminder;
    }

    private function validate(): void
    {
        $this->validateRules($this->data);

        $this->reminder = ContactReminder::where('contact_id', $this->data['contact_id'])
            ->findOrFail($this->data['contact_reminder_id']);
    }

    private function update(): void
    {
        $this->reminder->label = $this->data['label'];
        $this->reminder->day = $this->data['day'];
        $this->reminder->month = $this->data['month'];
        $this->reminder->year = $this->data['year'];
        $this->reminder->type = $this->data['type'];
        $this->reminder->frequency_number = $this->valueOrNull($this->data, 'frequency_number');
        $this->reminder->save();
    }

    private function deleteOldScheduledReminders(): void
    {
        DB::table('contact_reminder_scheduled')->where('contact_reminder_id', $this->reminder->id)->delete();
    }

    private function scheduledReminderForAllUsersInVault(): void
    {
        $users = $this->vault->users()->get();

        foreach ($users as $user) {
            (new ScheduleContactReminderForUser())->execute([
                'contact_reminder_id' => $this->reminder->id,
                'user_id' => $user->id,
            ]);
        }
    }

    private function updateLastUpdatedDate(): void
    {
        $this->contact->last_updated_at = Carbon::now();
        $this->contact->save();
    }
}
