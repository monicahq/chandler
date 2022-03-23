<?php

namespace App\Services\Contact\ManageLoan;

use Carbon\Carbon;
use App\Models\Note;
use App\Models\Emotion;
use App\Jobs\CreateAuditLog;
use App\Services\BaseService;
use App\Jobs\CreateContactLog;
use App\Models\ContactFeedItem;
use App\Interfaces\ServiceInterface;
use App\Models\Contact;
use App\Models\Loan;

class CreateLoan extends BaseService implements ServiceInterface
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
            'author_must_be_vault_editor',
            'contact_must_belong_to_vault',
        ];
    }

    /**
     * Create a loan.
     *
     * @param  array  $data
     * @return Loan
     */
    public function execute(array $data): Loan
    {
        $this->data = $data;
        $this->validate();
        $this->create();

        $this->contact->last_updated_at = Carbon::now();
        $this->contact->save();

        $this->log();
        $this->createFeedItem();

        return $this->loan;
    }

    private function validate(): void
    {
        $this->validateRules($this->data);

        $this->loaner = Contact::where('vault_id', $this->data['vault_id'])
            ->findOrFail('id', $this->data['loaner_id']);

        $this->loanee = Contact::where('vault_id', $this->data['vault_id'])
            ->findOrFail('id', $this->data['loanee_id']);
    }

    private function create(): void
    {
        $this->loan = Loan::create([
            'type' => $this->data['type'],
            'name' => $this->data['name'],
            'description' => $this->valueOrNull($this->data, 'description'),
            'amount_lent' => $this->valueOrNull($this->data, 'amount_lent'),
            'loaner_id' => $this->loaner->id,
            'loanee_id' => $this->loanee->id,
            'loaned_at' => $this->valueOrNull($this->data, 'loaned_at'),
        ]);
    }

    private function log(): void
    {
        CreateAuditLog::dispatch([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'author_name' => $this->author->name,
            'action_name' => 'loan_created',
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
            'action_name' => 'loan_created',
            'objects' => json_encode([
                'loan_name' => $this->loan->name,
            ]),
        ])->onQueue('low');
    }

    private function createFeedItem(): void
    {
        $feedItem = ContactFeedItem::create([
            'contact_id' => $this->contact->id,
            'action' => ContactFeedItem::ACTION_NOTE_CREATED,
        ]);
        $this->loan->feedItem()->save($feedItem);
    }
}
