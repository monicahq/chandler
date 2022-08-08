<?php

namespace App\Contact\ManageContactAddresses\Services;

use App\Helpers\MapHelper;
use App\Interfaces\ServiceInterface;
use App\Models\Address;
use App\Models\Company\Place;
use App\Services\BaseService;
use Illuminate\Http\Client\HttpClientException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GetGPSCoordinate extends BaseService implements ServiceInterface
{
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
            'address_id' => 'required|integer|exists:addresses,id',
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
        ];
    }

    /**
     * Get the latitude and longitude from a place.
     * This method uses LocationIQ to process the geocoding.
     * Should always be done through a job, and not be called directly.
     * You should use the FetchAddressGeocoding job for this.
     *
     * @param  array  $data
     * @return Address
     */
    public function execute(array $data): Address
    {
        $this->data = $data;
        $this->validateRules($data);

        $this->getCoordinates();

        return $this->address;
    }

    private function validate(): void
    {
        $this->validateRules($this->data);

        $this->address = Address::where('contact_id', $this->contact->id)
            ->findOrFail($this->data['address_id']);
    }

    private function getCoordinates(): void
    {
        $query = $this->buildQuery($place);

        if (is_null($query)) {
            return null;
        }

        try {
            $response = Http::get($query);
            $response->throw();

            $place->latitude = $response->json('0.lat');
            $place->longitude = $response->json('0.lon');
            $place->save();

            return $place;
        } catch (HttpClientException $e) {
            Log::error('Error calling location_iq: '.$e);
        }

        return null;
    }

    private function buildQuery(): ?string
    {
        if (is_null(config('officelife.location_iq_api_key'))) {
            return null;
        }

        $query = http_build_query([
            'format' => 'json',
            'key' => config('officelife.location_iq_api_key'),
            'q' => MapHelper::getAddressAsString($this->address),
        ]);

        return Str::finish(config('officelife.location_iq_url'), '/').'search.php?'.$query;
    }
}
