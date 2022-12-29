<?php

namespace App\Domains\Contact\ManageLifeEvents\Services;

use App\Interfaces\ServiceInterface;
use App\Models\LifeEvent;
use App\Models\LifeEventType;
use App\Services\BaseService;
use Carbon\Carbon;

class UpdateLifeEvent extends BaseService implements ServiceInterface
{
    private LifeEvent $lifeEvent;

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
            'life_event_type_id' => 'required|integer|exists:life_event_types,id',
            'contact_life_event_id' => 'required|integer|exists:contact_life_events,id',
            'summary' => 'required|string|max:255',
            'started_at' => 'date|date_format:Y-m-d',
            'ended_at' => 'date|date_format:Y-m-d',
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
     * Update a contact event.
     *
     * @param  array  $data
     * @return LifeEvent
     */
    public function execute(array $data): LifeEvent
    {
        $this->data = $data;
        $this->validate();
        $this->update();

        $this->contact->last_updated_at = Carbon::now();
        $this->contact->save();

        return $this->lifeEvent;
    }

    private function validate(): void
    {
        $this->validateRules($this->data);

        $this->lifeEvent = $this->contact->contactLifeEvents()
            ->findOrFail($this->data['contact_life_event_id']);

        $lifeEventType = LifeEventType::findOrFail($this->data['life_event_type_id']);

        $this->account()->lifeEventCategories()
            ->findOrFail($lifeEventType->lifeEventCategory->id);
    }

    private function update(): void
    {
        $this->lifeEvent->summary = $this->data['summary'];
        $this->lifeEvent->started_at = $this->data['started_at'];
        $this->lifeEvent->ended_at = $this->data['ended_at'];
        $this->lifeEvent->save();
    }
}
