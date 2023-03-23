<?php

namespace App\Domains\Contact\ManageGifts\Services;

use App\Interfaces\ServiceInterface;
use App\Models\Gift;
use App\Services\BaseService;

class DestroyGift extends BaseService implements ServiceInterface
{
    private Gift $gift;

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
            'gift_id' => 'required|integer|exists:gifts,id',
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
     * Destroy a gift.
     *
     * @param  array  $data
     */
    public function execute(array $data): void
    {
        $this->validateRules($data);

        $this->gift = $this->vault->gifts()->findOrFail($data['gift_id']);

        $this->gift->delete();
    }
}
