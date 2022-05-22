<?php

namespace App\Contact\ManageContactEvents\Services;

use App\Helpers\DateHelper;
use App\Interfaces\ServiceInterface;
use App\Models\Activity;
use App\Models\ActivityType;
use App\Models\ContactActivity;
use App\Models\ContactEvent;
use App\Models\ContactFeedItem;
use App\Models\Emotion;
use App\Services\BaseService;
use Carbon\Carbon;

class UpdateContactEvent extends BaseService implements ServiceInterface
{
    private ContactEvent $contactEvent;
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
            'contact_event_id' => 'required|integer|exists:contact_events,id',
            'summary' => 'required|string|max:255',
            'started_at' => 'date|format:Y-m-d',
            'ended_at' => 'date|format:Y-m-d',
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
     * @return ContactEvent
     */
    public function execute(array $data): ContactEvent
    {
        $this->data = $data;
        $this->validate();
        $this->update();

        $this->contact->last_updated_at = Carbon::now();
        $this->contact->save();

        $this->createFeedItem();

        return $this->contactEvent;
    }

    private function validate(): void
    {
        $this->validateRules($this->data);

        $this->contactEvent = ContactEvent::where('contact_id', $this->data['contact_id'])
            ->findOrFail($this->data['contact_event_id']);
    }

    private function update(): void
    {
        $this->contactEvent->summary = $this->data['summary'];
        $this->contactEvent->started_at = $this->data['started_at'];
        $this->contactEvent->ended_at = $this->data['ended_at'];
        $this->contactEvent->save();
    }

    private function createFeedItem(): void
    {
        $feedItem = ContactFeedItem::create([
            'author_id' => $this->author->id,
            'contact_id' => $this->contact->id,
            'action' => ContactFeedItem::ACTION_CONTACT_EVENT_UPDATED,
        ]);

        $this->contactEvent->feedItem()->save($feedItem);
    }
}
