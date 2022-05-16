<?php

namespace App\Contact\ManageCalls\Services;

use App\Interfaces\ServiceInterface;
use App\Models\Call;
use App\Models\ContactTask;
use App\Services\BaseService;
use Carbon\Carbon;

class UpdateCall extends BaseService implements ServiceInterface
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
            'vault_id' => 'required|integer|exists:vaults,id',
            'author_id' => 'required|integer|exists:users,id',
            'contact_id' => 'required|integer|exists:contacts,id',
            'call_id' => 'required|integer|exists:calls,id',
            'called_at' => 'required|date_format:Y-m-d',
            'duration' => 'nullable|integer',
            'type' => 'required|string',
            'answered' => 'nullable|boolean',
            'who_initiated' => 'required|string',
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
     * Update a call.
     *
     * @param  array  $data
     * @return Call
     */
    public function execute(array $data): Call
    {
        $this->validateRules($data);

        $call = Call::where('contact_id', $data['contact_id'])
            ->findOrFail($data['call_id']);

        $call->called_at = $data['called_at'];
        $call->duration = $this->valueOrNull($data, 'duration');
        $call->type = $data['type'];
        $call->answered = $this->valueOrTrue($data, 'answered');
        $call->who_initiated = $data['who_initiated'];
        $call->save();

        $this->contact->last_updated_at = Carbon::now();
        $this->contact->save();

        return $call;
    }
}
