<?php

namespace App\Contact\ManageCouple\Services;

use App\Models\Couple;
use App\Services\BaseService;
use App\Interfaces\ServiceInterface;

class CreateCouple extends BaseService implements ServiceInterface
{
    private Couple $couple;

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
            'name' => 'nullable|string|max:255',
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
     * Create a couple.
     *
     * @param  array  $data
     * @return Couple
     */
    public function execute(array $data): Couple
    {
        $this->validateRules($data);

        $this->couple = Couple::create([
            'vault_id' => $data['vault_id'],
            'name' => $this->valueOrNull($data, 'name'),
        ]);

        return $this->couple;
    }
}
