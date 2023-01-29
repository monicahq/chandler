<?php

namespace App\Domains\Contact\ManageLifeEvents\Services;

use App\Interfaces\ServiceInterface;
use App\Models\Contact;
use App\Models\LifeEvent;
use App\Models\LifeEventType;
use App\Services\BaseService;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ToggleLifeEvent extends BaseService implements ServiceInterface
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
            'timeline_event_id' => 'required|integer|exists:timeline_events,id',
            'life_event_id' => 'required|integer|exists:life_events,id',
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
     * Toggle a life event.
     *
     * @param  array  $data
     * @return LifeEvent
     */
    public function execute(array $data): LifeEvent
    {
        $this->data = $data;
        $this->validate();
        $this->update();

        return $this->lifeEvent;
    }

    private function validate(): void
    {
        $this->validateRules($this->data);

        $timelineEvent = $this->vault->timelineEvents()
            ->findOrFail($this->data['timeline_event_id']);

        $this->lifeEvent = $timelineEvent->lifeEvents()
            ->findOrFail($this->data['life_event_id']);
    }

    private function update(): void
    {
        $this->lifeEvent->collapsed = !$this->lifeEvent->collapsed;
        $this->lifeEvent->save();
    }
}
