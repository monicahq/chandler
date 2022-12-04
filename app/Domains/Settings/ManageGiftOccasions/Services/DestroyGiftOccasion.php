<?php

namespace App\Domains\Settings\ManageGiftOccasions\Services;

use App\Interfaces\ServiceInterface;
use App\Models\GiftOccasion;
use App\Services\BaseService;

class DestroyGiftOccasion extends BaseService implements ServiceInterface
{
    private GiftOccasion $giftOccasion;

    /**
     * Get the validation rules that apply to the service.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'account_id' => 'required|integer|exists:accounts,id',
            'gift_occasion_id' => 'required|integer|exists:gift_occasions,id',
            'author_id' => 'required|integer|exists:users,id',
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
     * Destroy a gift occasion.
     *
     * @param  array  $data
     */
    public function execute(array $data): void
    {
        $this->validateRules($data);

        $this->giftOccasion = $this->account()->giftOccasions()
            ->findOrFail($data['gift_occasion_id']);

        $this->giftOccasion->delete();

        $this->repositionEverything();
    }

    private function repositionEverything(): void
    {
        $this->account()->giftOccasions()
            ->where('position', '>', $this->giftOccasion->position)
            ->decrement('position');
    }
}
