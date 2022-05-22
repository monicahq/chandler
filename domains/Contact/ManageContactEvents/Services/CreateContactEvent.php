<?php

namespace App\Contact\ManageContactEvents\Services;

use App\Interfaces\ServiceInterface;
use App\Models\Contact;
use App\Models\ContactEvent;
use App\Models\ContactFeedItem;
use App\Services\BaseService;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class CreateContactEvent extends BaseService implements ServiceInterface
{
    private ContactEvent $contactEvent;
    private array $data;
    private Collection $participantsCollection;

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
            'author_must_be_vault_editor',
            'contact_must_belong_to_vault',
        ];
    }

    /**
     * Create a contact event.
     *
     * @param  array  $data
     * @return ContactEvent
     */
    public function execute(array $data): ContactEvent
    {
        $this->data = $data;
        $this->validate();
        $this->store();
        $this->createFeedItem();

        return $this->contactEvent;
    }

    private function validate(): void
    {
        $this->validateRules($this->data);
    }

    private function store(): void
    {
        $this->contactEvent = ContactEvent::create([
            'contact_id' => $this->data['contact_id'],
            'summary' => $this->data['summary'],
            'started_at' => $this->data['started_at'],
            'ended_at' => $this->data['ended_at'],
        ]);

        $this->contact->last_updated_at = Carbon::now();
        $this->contact->save();
    }

    private function createFeedItem(): void
    {
        $feedItem = ContactFeedItem::create([
            'author_id' => $this->author->id,
            'contact_id' => $this->contact->id,
            'action' => ContactFeedItem::ACTION_CONTACT_EVENT_CREATED,
        ]);
        $this->contactEvent->feedItem()->save($feedItem);
    }
}
