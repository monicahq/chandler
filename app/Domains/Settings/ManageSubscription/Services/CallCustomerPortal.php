<?php

namespace App\Domains\Settings\ManageSubscription\Services;

use App\Exceptions\CustomerPortalWrongCredentials;
use App\Exceptions\NoCustomerPortalSecretsException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Interfaces\ServiceInterface;
use App\Services\BaseService;

class CallCustomerPortal extends BaseService implements ServiceInterface
{
    private array $data;

    /**
     * Get the validation rules that apply to the service.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'licence_key' => 'required|string:4096',
        ];
    }

    /**
     * Make a call to the customer portal.
     *
     * @param  array  $data
     * @return array
     */
    public function execute(array $data): array
    {
        $this->data = $data;
        $this->validateRules($data);

        $this->validateEnvVariables();

        $accessToken = $this->getAccessToken();
        $response = $this->makeRequestToCustomerPortal($accessToken);

        return [
            'status' => $response->status(),
            'data' => $response->json(),
        ];
    }

    private function validateEnvVariables(): void
    {
        if (config('monica.customer_portal_url') === '') {
            throw new NoCustomerPortalSecretsException();
        }

        if (config('monica.customer_portal_client_id') === null || config('monica.customer_portal_client_secret') === null) {
            throw new NoCustomerPortalSecretsException();
        }
    }

    private function getAccessToken(): string
    {
        return Cache::remember('customer_portal.access_token', 31449600 /* 364 days */, function () {
            $url = config('monica.customer_portal_url') . '/oauth/token';

            $response = Http::asForm()->post($url, [
                'grant_type' => 'client_credentials',
                'client_id' => config('monica.customer_portal_client_id'),
                'client_secret' => config('monica.customer_portal_client_secret'),
                'scope' => 'manage-key',
            ]);

            $json = $response->json();
            if ($response->failed() || !isset($json['access_token'])) {
                throw new CustomerPortalWrongCredentials();
            }

            return $json['access_token'];
        });
    }

    private function makeRequestToCustomerPortal(string $accessToken): Response
    {
        $url = config('monica.customer_portal_url') . '/api/validate';

        return Http::withToken($accessToken)
            ->acceptJson()
            ->post($url, [
                'licence_key' => $this->data['licence_key'],
            ]);
    }
}
