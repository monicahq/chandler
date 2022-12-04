<?php

namespace App\Domains\Contact\ManageGifts\Services;

use App\Interfaces\ServiceInterface;
use App\Models\Contact;
use App\Models\Gift;
use App\Services\BaseService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class CreateGift extends BaseService implements ServiceInterface
{
    private Gift $gift;

    private Collection $donatorsCollection;

    private Collection $recipientsCollection;

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
            'gift_occasion_id' => 'nullable|integer|exists:gift_occasions,id',
            'gift_state_id' => 'nullable|integer|exists:gift_states,id',
            'currency_id' => 'nullable|integer|exists:currencies,id',
            'name' => 'required|string|max:65535',
            'description' => 'nullable|string|max:65535',
            'budget' => 'nullable|integer',
            'donators_ids' => 'required',
            'recipients_ids' => 'required',
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
     * Create a gift.
     *
     * @param  array  $data
     * @return Gift
     */
    public function execute(array $data): Gift
    {
        $this->data = $data;
        $this->validate();
        $this->create();

        $this->contact->last_updated_at = Carbon::now();
        $this->contact->save();

        return $this->gift;
    }

    private function validate(): void
    {
        $this->validateRules($this->data);

        $this->donatorsCollection = collect();
        foreach ($this->data['donators_ids'] as $donatorId) {
            $this->donatorsCollection->push(
                Contact::where('vault_id', $this->data['vault_id'])
                    ->findOrFail($donatorId)
            );
        }

        $this->recipientsCollection = collect();
        foreach ($this->data['recipients_ids'] as $recipientId) {
            $this->recipientsCollection->push(
                Contact::where('vault_id', $this->data['vault_id'])
                    ->findOrFail($recipientId)
            );
        }
    }

    private function create(): void
    {
        $this->gift = Gift::create([
            'vault_id' => $this->data['vault_id'],
            'gift_occasion_id' => $this->valueOrNull($this->data, 'gift_occasion_id'),
            'gift_state_id' => $this->valueOrNull($this->data, 'gift_state_id'),
            'name' => $this->data['name'],
            'description' => $this->valueOrNull($this->data, 'description'),
            'budget' => $this->valueOrNull($this->data, 'budget'),
            'currency_id' => $this->valueOrNull($this->data, 'currency_id'),
            'uuid' => (string) Str::uuid(),
        ]);

        foreach ($this->donatorsCollection as $donator) {
            foreach ($this->recipientsCollection as $loanee) {
                $donator->loansAsLoaner()->syncWithoutDetaching([$this->gift->id => ['loanee_id' => $loanee->id]]);
            }
        }

        foreach ($this->recipientsCollection as $loanee) {
            foreach ($this->donatorsCollection as $loaner) {
                $loanee->loansAsLoanee()->syncWithoutDetaching([$this->gift->id => ['loaner_id' => $loaner->id]]);
            }
        }
    }
}
