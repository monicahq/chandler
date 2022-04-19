<?php

namespace App\Contact\ManageLoans\Web\ViewHelpers;

use Carbon\Carbon;
use App\Models\Loan;
use App\Models\User;
use App\Models\Contact;
use App\Helpers\DateHelper;

class ModuleLoanViewHelper
{
    public static function data(Contact $contact, User $user): array
    {
        $loansAsLoaner = $contact->loansAsLoaner()->get();
        $loansAsLoanee = $contact->loansAsLoanee()->get();

        $loans = $loansAsLoaner->concat($loansAsLoanee)->unique('id');

        $loansAssociatedWithContactCollection = $loans->map(function ($loan) use ($contact, $user) {
            return self::dtoLoan($loan, $contact, $user);
        });

        return [
            'loans' => $loansAssociatedWithContactCollection,
            'current_date' => Carbon::now()->format('Y-m-d'),
            'url' => [
                'currencies' => route('currencies.index'),
                'store' => route('contact.loan.store', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                ]),
            ],
        ];
    }

    public static function dtoLoan(Loan $loan, Contact $contact, User $user): array
    {
        $loaners = $loan->loaners->unique('id');
        $loanees = $loan->loanees->unique('id');
        $loanersCollection = $loaners->map(function ($loaner) use ($user) {
            return [
                'id' => $loaner->id,
                'name' => $loaner->getName($user),
            ];
        });
        $loaneesCollection = $loanees->map(function ($loanee) use ($user) {
            return [
                'id' => $loanee->id,
                'name' => $loanee->getName($user),
            ];
        });

        return [
            'id' => $loan->id,
            'type' => $loan->type,
            'name' => $loan->name,
            'description' => $loan->description,
            'amount_lent' => $loan->amount_lent / 100,
            'currency_id' => $loan->currency_id,
            'loaned_at' => $loan->loaned_at->format('Y-m-d'),
            'loaned_at_human_format' => DateHelper::format($loan->loaned_at, $user),
            'loaners' => $loanersCollection,
            'loanees' => $loaneesCollection,
            'url' => [
                'update' => route('contact.loan.update', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                    'loan' => $loan->id,
                ]),
                'destroy' => route('contact.loan.destroy', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                    'loan' => $loan->id,
                ]),
            ],
        ];
    }
}