<?php

namespace App\Contact\ManageContactEvents\Services;

use App\Interfaces\ServiceInterface;
use App\Models\ContactEvent;
use App\Models\ContactFeedItem;
use App\Services\BaseService;
use Carbon\Carbon;

class DestroyContactEvent extends BaseService implements ServiceInterface
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
            'contact_id' => 'required|integer|exists:contacts,id',
            'contact_event_id' => 'required|integer|exists:contact_events,id',
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
     * Destroy a contact activity.
     *
     * @param  array  $data
     */
    public function execute(array $data): void
    {
        $this->validate();
        $this->data = $data;

        $this->contactEvent->delete();

        $this->contact->last_updated_at = Carbon::now();
        $this->contact->save();

        $this->createFeedItem();
    }

    private function validate(): void
    {
        $this->validateRules($this->data);

        $this->contactEvent = ContactEvent::where('contact_id', $this->data['contact_id'])
            ->findOrFail($this->data['contact_event_id']);
    }

    private function createFeedItem(): void
    {
        $feedItem = ContactFeedItem::create([
            'author_id' => $this->author->id,
            'contact_id' => $this->contact->id,
            'action' => ContactFeedItem::ACTION_CONTACT_EVENT_DESTROYED,
        ]);

        $this->contactEvent->feedItem()->save($feedItem);
    }
}
