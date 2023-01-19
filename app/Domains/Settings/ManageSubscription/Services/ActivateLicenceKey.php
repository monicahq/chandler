<?php

namespace App\Domains\Settings\ManageSubscription\Services;

use App\Exceptions\LicenceKeyDontExistException;
use App\Exceptions\LicenceKeyErrorException;
use App\Exceptions\LicenceKeyInvalidException;
use App\Exceptions\MissingPrivateKeyException;
use App\Interfaces\ServiceInterface;
use App\Models\Account;
use App\Services\BaseService;

class ActivateLicenceKey extends BaseService implements ServiceInterface
{
    private Account $account;

    private array $data;

    private int $status;

    private array $response;

    /**
     * Get the validation rules that apply to the service.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'account_id' => 'required|integer|exists:accounts,id',
            'licence_key' => 'required|string:4096',
        ];
    }

    /**
     * Check if the licence key given by the user is a valid licence key.
     * If it is, activate the licence key and set the valid_until_at date.
     *
     * @param  array  $data
     * @return void
     */
    public function execute(array $data): void
    {
        $this->validateRules($data);
        $this->data = $data;
        $this->account = Account::findOrFail($data['account_id']);

        $this->validateEnvVariables();
        $this->makeRequestToCustomerPortal();
        $this->checkResponseCode();
        $this->store();
    }

    private function validateEnvVariables(): void
    {
        if (config('monica.customer_portal_private_key') === null) {
            throw new MissingPrivateKeyException();
        }
    }

    private function makeRequestToCustomerPortal(): void
    {
        $data = (new CallCustomerPortal())->execute([
            'licence_key' => $this->data['licence_key'],
        ]);

        $this->status = $data['status'];
        $this->response = $data['data'];
    }

    private function checkResponseCode(): void
    {
        if ($this->status === 404) {
            throw new LicenceKeyDontExistException();
        }

        if ($this->status === 410) {
            throw new LicenceKeyInvalidException();
        }

        if ($this->status !== 200) {
            throw new LicenceKeyErrorException();
        }
    }

    private function store(): void
    {
        $licenceKey = $this->decodeKey();

        $this->account->licence_key = $this->data['licence_key'];
        $this->account->valid_until_at = $this->response['next_check_at'];
        $this->account->purchaser_email = $licenceKey['purchaser_email'];
        switch ($licenceKey['frequency']) {
            case 'month':
                $this->account->frequency = 'monthly';
                break;
            case 'year':
                $this->account->frequency = 'annual';
                break;
            default:
                $this->account->frequency = $licenceKey['frequency'];
                break;
        }

        $this->account->save();
    }

    private function decodeKey(): array
    {
        $encrypter = app('license.encrypter');

        return $encrypter->decrypt($this->data['licence_key']);
    }
}
