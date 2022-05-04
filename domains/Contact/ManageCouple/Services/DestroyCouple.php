<?php

namespace App\Contact\ManageCouple\Services;

use App\Models\Couple;
use App\Services\BaseService;
use App\Interfaces\ServiceInterface;

class DestroyCouple extends BaseService implements ServiceInterface
{
    private Couple $couple;
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
            'couple_id' => 'required|integer|exists:couples,id',
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
        ];
    }

    /**
     * Destroy a couple.
     *
     * @param  array  $data
     */
    public function execute(array $data): void
    {
        $this->validateRules($data);

        $this->couple = Couple::where('vault_id', $data['vault_id'])
            ->findOrFail($data['couple_id']);

        $this->couple->delete();
    }
}
