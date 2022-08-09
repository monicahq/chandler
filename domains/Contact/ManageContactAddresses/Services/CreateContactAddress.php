<?php

namespace App\Contact\ManageContactAddresses\Services;

use App\Contact\ManageContactAddresses\Jobs\FetchAddressGeocoding;
use App\Interfaces\ServiceInterface;
use App\Jobs\CreateAuditLog;
use App\Jobs\CreateContactLog;
use App\Models\Address;
use App\Models\AddressType;
use App\Services\BaseService;
use Carbon\Carbon;

class CreateContactAddress extends BaseService implements ServiceInterface
{
    private AddressType $addressType;

    private Address $address;

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
            'address_type_id' => 'nullable|integer|exists:address_types,id',
            'street' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'lived_from_at' => 'nullable|date_format:Y-m-d',
            'lived_until_at' => 'nullable|date_format:Y-m-d',
            'is_past_address' => 'nullable|boolean',
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
     * Create a contact address.
     *
     * @param  array  $data
     * @return Address
     */
    public function execute(array $data): Address
    {
        $this->validateRules($data);

        if ($this->valueOrNull($data, 'address_type_id')) {
            $this->addressType = AddressType::where('account_id', $data['account_id'])
                ->findOrFail($data['address_type_id']);
        }

        $this->address = Address::create([
            'contact_id' => $data['contact_id'],
            'address_type_id' => $this->valueOrNull($data, 'address_type_id'),
            'street' => $this->valueOrNull($data, 'street'),
            'city' => $this->valueOrNull($data, 'city'),
            'province' => $this->valueOrNull($data, 'province'),
            'postal_code' => $this->valueOrNull($data, 'postal_code'),
            'country' => $this->valueOrNull($data, 'country'),
            'latitude' => $this->valueOrNull($data, 'latitude'),
            'longitude' => $this->valueOrNull($data, 'longitude'),
            'lived_from_at' => $this->valueOrNull($data, 'lived_from_at'),
            'lived_until_at' => $this->valueOrNull($data, 'lived_until_at'),
            'is_past_address' => $this->valueOrFalse($data, 'is_past_address'),
        ]);

        $this->contact->last_updated_at = Carbon::now();
        $this->contact->save();

        $this->geocodePlace();
        $this->log();

        return $this->address;
    }

    /**
     * Fetch the longitude/latitude for the new address.
     * This is placed on a queue so it doesn't slow down the app.
     *
     * @param  Address  $address
     */
    private function geocodePlace(): void
    {
        FetchAddressGeocoding::dispatch($this->address)->onQueue('low');
    }

    private function log(): void
    {
        CreateAuditLog::dispatch([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'author_name' => $this->author->name,
            'action_name' => 'contact_address_created',
            'objects' => json_encode([
                'contact_id' => $this->contact->id,
                'contact_name' => $this->contact->name,
                'address_type_name' => isset($this->addressType) ? $this->addressType->name : null,
            ]),
        ])->onQueue('low');

        CreateContactLog::dispatch([
            'contact_id' => $this->contact->id,
            'author_id' => $this->author->id,
            'author_name' => $this->author->name,
            'action_name' => 'contact_address_created',
            'objects' => json_encode([
                'address_type_name' => isset($this->addressType) ? $this->addressType->name : null,
            ]),
        ])->onQueue('low');
    }
}
