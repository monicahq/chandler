<?php

namespace App\Domains\Contact\ManageLifeEvents\Services;

use App\Interfaces\ServiceInterface;
use App\Models\Contact;
use App\Models\TimelineEvent;
use App\Services\BaseService;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class CreateTimelineEvent extends BaseService implements ServiceInterface
{
    private TimelineEvent $timelineEvent;

    private Collection $participantsCollection;

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
            'label' => 'nullable|string|max:255',
            'started_at' => 'required|date|date_format:Y-m-d',
            'participant_ids' => 'required|array',
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
        ];
    }

    /**
     * Create a timeline event.
     * A timeline event is a part of one or more contacts lives, and is itself
     * composed of one or more life events.
     *
     * @param  array  $data
     * @return TimelineEvent
     */
    public function execute(array $data): TimelineEvent
    {
        $this->data = $data;
        $this->validate();
        $this->store();
        $this->associateParticipants();
        $this->updateLastEditedDate();

        return $this->timelineEvent;
    }

    private function validate(): void
    {
        $this->validateRules($this->data);

        $this->participantsCollection = collect($this->data['participant_ids'])
            ->map(fn (int $participantId): Contact => $this->vault->contacts()->findOrFail($participantId));
    }

    private function associateParticipants(): void
    {
        foreach ($this->participantsCollection as $participant) {
            $participant->timelineEvents()->attach($this->timelineEvent->id);
        }
    }

    private function updateLastEditedDate(): void
    {
        foreach ($this->participantsCollection as $participant) {
            $participant->last_updated_at = Carbon::now();
            $participant->save();
        }
    }

    private function store(): void
    {
        $this->timelineEvent = TimelineEvent::create([
            'vault_id' => $this->data['vault_id'],
            'label' => $this->valueOrNull($this->data, 'summary'),
            'started_at' => $this->data['started_at'],
        ]);
    }
}
