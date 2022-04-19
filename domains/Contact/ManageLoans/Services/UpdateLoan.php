<?php

namespace App\Contact\ManageLoans\Services;

use Carbon\Carbon;
use App\Models\Loan;
use App\Models\Contact;
use App\Jobs\CreateAuditLog;
use App\Services\BaseService;
use App\Jobs\CreateContactLog;
use App\Models\ContactFeedItem;
use App\Interfaces\ServiceInterface;

class UpdateLoan extends BaseService implements ServiceInterface
{
    private Loan $loan;
    private Contact $loaner;
    private Contact $loanee;
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
            'contact_id' => 'required|integer|exists:contacts,id',
            'loan_id' => 'required|integer|exists:loans,id',
            'currency_id' => 'nullable|integer|exists:currencies,id',
            'type' => 'required|string|max:255',
            'name' => 'required|string|max:65535',
            'description' => 'nullable|string|max:65535',
            'loaner_id' => 'required|integer|exists:contacts,id',
            'loanee_id' => 'required|integer|exists:contacts,id',
            'amount_lent' => 'nullable|integer',
            'loaned_at' => 'nullable|date_format:Y-m-d',
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
     * Update a loan.
     *
     * @param  array  $data
     * @return Loan
     */
    public function execute(array $data): Loan
    {
        $this->data = $data;
        $this->validate();
        $this->update();
        $this->createFeedItem();
        $this->log();

        return $this->loan;
    }

    private function validate(): void
    {
        $this->validateRules($this->data);

        $this->loan = Loan::where('contact_id', $this->data['contact_id'])
            ->findOrFail($this->data['loan_id']);

        $this->loaner = Contact::where('vault_id', $this->data['vault_id'])
            ->findOrFail($this->data['loaner_id']);

        $this->loanee = Contact::where('vault_id', $this->data['vault_id'])
            ->findOrFail($this->data['loanee_id']);
    }

    private function update(): void
    {
        $this->loan->type = $this->data['type'];
        $this->loan->name = $this->data['name'];
        $this->loan->description = $this->valueOrNull($this->data, 'description');
        $this->loan->amount_lent = $this->valueOrNull($this->data, 'amount_lent');
        $this->loan->loaned_at = $this->valueOrNull($this->data, 'loaned_at');
        $this->loan->currency_id = $this->valueOrNull($this->data, 'currency_id');
        $this->loan->save();

        $this->loaner->loansAsLoaner()->syncWithoutDetaching([$this->loan->id => ['loanee_id' => $this->loanee->id]]);

        $this->contact->last_updated_at = Carbon::now();
        $this->contact->save();
    }

    private function log(): void
    {
        CreateAuditLog::dispatch([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'author_name' => $this->author->name,
            'action_name' => 'loan_updated',
            'objects' => json_encode([
                'contact_id' => $this->contact->id,
                'contact_name' => $this->contact->name,
                'loan_name' => $this->loan->name,
            ]),
        ])->onQueue('low');

        CreateContactLog::dispatch([
            'contact_id' => $this->contact->id,
            'author_id' => $this->author->id,
            'author_name' => $this->author->name,
            'action_name' => 'loan_updated',
            'objects' => json_encode([
                'loan_name' => $this->loan->name,
            ]),
        ])->onQueue('low');
    }

    private function createFeedItem(): void
    {
        $feedItem = ContactFeedItem::create([
            'contact_id' => $this->contact->id,
            'action' => ContactFeedItem::ACTION_LOAN_UPDATED,
        ]);
        $this->loan->feedItem()->save($feedItem);
    }
}
