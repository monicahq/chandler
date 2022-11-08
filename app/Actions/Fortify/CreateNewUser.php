<?php

namespace App\Actions\Fortify;

use App\Domains\Settings\CreateAccount\Services\CreateAccount;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array  $input
     * @return \App\Models\User
     */
    public function create(array $input)
    {

        SIZE = 'max:255';

        Validator::make($input, [
            'first_name' => ['required', 'string', SIZE],
            'last_name' => ['required', 'string', SIZE],
            'email' => ['required', 'string', 'email', SIZE, 'unique:users'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        return app(CreateAccount::class)->execute([
            'first_name' => $input['first_name'],
            'last_name' => $input['last_name'],
            'email' => $input['email'],
            'password' => isset($input['password']) ? Hash::make($input['password']) : null,
        ]);
    }
}
