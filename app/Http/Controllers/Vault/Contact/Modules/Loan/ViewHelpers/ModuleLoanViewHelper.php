<?php

namespace App\Http\Controllers\Vault\Contact\Modules\Label\ViewHelpers;

use App\Models\Label;
use App\Models\Contact;
use App\Models\Loan;
use App\Models\User;

class ModuleLoanViewHelper
{
    public static function data(Contact $contact, User $user): array
    {
        $loans = $contact->loans;

        $loansAssociatedWithContactCollection = $loans->map(function ($loan) use ($contact, $user) {
            return self::dtoLoan($loan, $contact, $user);
        });

        return [
            'labels_in_contact' => $loansAssociatedWithContactCollection,
            'labels_in_vault' => $loansInVaultCollection,
            'url' => [
                'store' => route('contact.label.store', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                ]),
                'update' => route('contact.date.index', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                ]),
            ],
        ];
    }

    public static function dtoLoan(Loan $loan, Contact $contact, User $user): array
    {
        $loaners = $loan->loaners;
        $loanersCollection = $loaners->map(function ($loaner) use ($user) {
            return [
                'id' => $loaner->id,
                'name' => $loaner->getName($user),
            ];
        });
        $loanees = $loan->loanees;
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
                'update' => route('contact.label.update', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                    'label' => $loan->id,
                ]),
                'destroy' => route('contact.label.destroy', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                    'label' => $loan->id,
                ]),
            ],
        ];
    }
}
