<?php

namespace App\Contact\ManageLifeContactEvents\Services;

use App\Interfaces\ServiceInterface;
use App\Models\Activity;
use App\Models\ActivityType;
use App\Models\Contact;
use App\Models\ContactActivity;
use App\Models\ContactFeedItem;
use App\Models\Emotion;
use App\Services\BaseService;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class CreateContactActivity extends BaseService implements ServiceInterface
{
    private ContactActivity $contactActivity;
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
            'activity_id' => 'required|integer|exists:activities,id',
            'emotion_id' => 'nullable|integer|exists:emotions,id',
            'summary' => 'required|string|max:255',
            'description' => 'nullable|string|max:65535',
            'happened_at' => 'date|format:Y-m-d',
            'period_of_day' => 'nullable|string|max:15',
            'participants' => 'required|array',
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
     * Create a contact activity.
     *
     * @param  array  $data
     * @return ContactActivity
     */
    public function execute(array $data): ContactActivity
    {
        $this->data = $data;
        $this->validate();
        $this->store();
        $this->createFeedItem();

        return $this->contactActivity;
    }

    private function validate(): void
    {
        $this->validateRules($this->data);

        if ($this->valueOrNull($this->data, 'emotion_id')) {
            Emotion::where('account_id', $this->data['account_id'])
                ->where('id', $this->data['emotion_id'])
                ->firstOrFail();
        }

        $activity = Activity::findOrFail($this->data['activity_id']);

        ActivityType::where('account_id', $this->data['account_id'])
            ->where('id', $activity->id)
            ->firstOrFail();

        $this->participantsCollection = collect();
        foreach ($this->data['participants'] as $contact) {
            $this->participantsCollection->push(
                Contact::where('vault_id', $this->data['vault_id'])
                    ->findOrFail($contact)
            );
        }
    }

    private function store(): void
    {
        $this->contactActivity = ContactActivity::create([
            'activity_id' => $this->data['activity_id'],
            'emotion_id' => $this->valueOrNull($this->data, 'emotion_id'),
            'summary' => $this->data['summary'],
            'description' => $this->valueOrNull($this->data, 'description'),
            'happened_at' => $this->data['happened_at'],
            'period_of_day' => $this->valueOrNull($this->data, 'period_of_day'),
        ]);

        // there is at least one contact
        $this->contact->activities()->syncWithoutDetaching([$this->contactActivity->id]);

        // now, let's add the other participants
        foreach ($this->participantsCollection as $contact) {
            $contact->activities()->syncWithoutDetaching([$this->contactActivity->id]);
        }

        $this->contact->last_updated_at = Carbon::now();
        $this->contact->save();
    }

    private function createFeedItem(): void
    {
        $feedItem = ContactFeedItem::create([
            'author_id' => $this->author->id,
            'contact_id' => $this->contact->id,
            'action' => ContactFeedItem::ACTION_CONTACT_ACTIVITY_CREATED,
        ]);
        $this->note->feedItem()->save($feedItem);
    }
}
