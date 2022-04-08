<?php

namespace App\Contact\ManageLoans\Web\ViewHelpers;

use App\Models\Loan;
use App\Models\User;
use App\Models\Contact;

class ModuleLoanViewHelper
{
    public static function data(Contact $contact, User $user): array
    {
        $loans = $contact->loans;

        $loansAssociatedWithContactCollection = $loans->map(function ($loan) use ($contact, $user) {
            return self::dtoLoan($loan, $contact, $user);
        });

        $currenciesCollection = $user->account->currencies()
            ->where('active', true)->get()->map(function ($currency) {
                return [
                    'id' => $currency->id,
                    'name' => $currency->code,
                ];
            });

        return [
            'loans' => $loansAssociatedWithContactCollection,
            'currencies' => $currenciesCollection,
            'url' => [
                'store' => route('contact.loan.store', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                ]),
            ],
        ];
    }

    public static function dtoLoan(Loan $loan, Contact $contact, User $user): array
    {
        $loaners = $loan->loaners;
        $loanees = $loan->loanees;
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
            'amount_lent' => $loan->amount_lent,
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
