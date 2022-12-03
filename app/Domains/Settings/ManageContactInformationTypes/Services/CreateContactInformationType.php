<?php

namespace App\Domains\Settings\ManageContactInformationTypes\Services;

use App\Interfaces\ServiceInterface;
use App\Models\ContactInformationType;
use App\Services\BaseService;

class CreateContactInformationType extends BaseService implements ServiceInterface
{
    /**
     * Get the validation rules that apply to the service.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'account_id' => 'required|integer|exists:accounts,id',
            'author_id' => 'required|integer|exists:users,id',
            'name' => 'required|string|max:255',
            'protocol' => 'nullable|string|max:255',
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
     * Create a contact information type.
     *
     * @param  array  $data
     * @return ContactInformationType
     */
    public function execute(array $data): ContactInformationType
    {
        $this->validateRules($data);

        $type = ContactInformationType::create([
            'account_id' => $data['account_id'],
            'name' => $data['name'],
            'protocol' => $this->valueOrNull($data, 'protocol'),
        ]);

        return $type;
    }
}
