<?php

namespace App\Contact\ManageContactActivities\Services;

use App\Helpers\DateHelper;
use App\Interfaces\ServiceInterface;
use App\Models\Activity;
use App\Models\ActivityType;
use App\Models\ContactActivity;
use App\Models\ContactFeedItem;
use App\Models\Emotion;
use App\Services\BaseService;
use Carbon\Carbon;

class UpdateContactActivity extends BaseService implements ServiceInterface
{
    private ContactActivity $contactActivity;
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
            'contact_activity_id' => 'required|integer|exists:contact_activities,id',
            'activity_id' => 'required|integer|exists:activities,id',
            'emotion_id' => 'nullable|integer|exists:emotions,id',
            'summary' => 'required|string|max:255',
            'description' => 'nullable|string|max:65535',
            'happened_at' => 'date|format:Y-m-d',
            'period_of_day' => 'nullable|string|max:15',
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
     * Update a contact activity.
     *
     * @param  array  $data
     * @return ContactActivity
     */
    public function execute(array $data): ContactActivity
    {
        $this->data = $data;
        $this->validate();
        $this->update();

        $this->contact->last_updated_at = Carbon::now();
        $this->contact->save();

        $this->createFeedItem();

        return $this->contactActivity;
    }

    private function validate(): void
    {
        $this->validateRules($this->data);

        $this->contactActivity = ContactActivity::where('contact_id', $this->data['contact_id'])
            ->findOrFail($this->data['contact_activity_id']);

        if ($this->valueOrNull($this->data, 'emotion_id')) {
            Emotion::where('account_id', $this->data['account_id'])
                ->where('id', $this->data['emotion_id'])
                ->firstOrFail();
        }

        $activity = Activity::findOrFail($this->data['activity_id']);

        ActivityType::where('account_id', $this->data['account_id'])
            ->where('id', $activity->id)
            ->firstOrFail();
    }

    private function update(): void
    {
        $this->contactActivity->activity_id = $this->data['activity_id'];
        $this->contactActivity->emotion_id = $this->valueOrNull($this->data, 'emotion_id');
        $this->contactActivity->summary = $this->data['summary'];
        $this->contactActivity->description = $this->valueOrNull($this->data, 'description');
        $this->contactActivity->happened_at = $this->data['happened_at'];
        $this->contactActivity->period_of_day = $this->valueOrNull($this->data, 'period_of_day');
        $this->contactActivity->save();
    }

    private function createFeedItem(): void
    {
        $feedItem = ContactFeedItem::create([
            'author_id' => $this->author->id,
            'contact_id' => $this->contact->id,
            'action' => ContactFeedItem::ACTION_CONTACT_ACTIVITY_UPDATED,
            'description' => $this->contactActivity->summary.' on '.DateHelper::format($this->contactActivity->happened_at, $this->author),
        ]);

        $this->contactActivity->feedItem()->save($feedItem);
    }
}
