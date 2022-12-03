<?php

namespace App\Domains\Settings\ManageActivityTypes\Services;

use App\Interfaces\ServiceInterface;
use App\Models\Activity;
use App\Models\User;
use App\Services\BaseService;

class DestroyActivity extends BaseService implements ServiceInterface
{
    /**
     * Get the validation rules that apply to the service.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'account_id' => 'required|integer|exists:accounts,id',
            'author_id' => 'required|integer|exists:users,id',
            'activity_type_id' => 'required|integer|exists:activity_types,id',
            'activity_id' => 'required|integer|exists:activities,id',
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
            'author_must_be_account_administrator',
        ];
    }

    /**
     * Destroy an activity.
     *
     * @param  array  $data
     */
    public function execute(array $data): void
    {
        $this->validateRules($data);

        $this->account()->activityTypes()
            ->findOrFail($data['activity_type_id']);

        $activity = Activity::where('activity_type_id', $data['activity_type_id'])
            ->findOrFail($data['activity_id']);

        $activity->delete();
    }
}
