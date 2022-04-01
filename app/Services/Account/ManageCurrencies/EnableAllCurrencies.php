<?php

namespace App\Services\Account\ManageCurrencies;

use App\Models\User;
use App\Models\Gender;
use App\Jobs\CreateAuditLog;
use App\Services\BaseService;
use App\Interfaces\ServiceInterface;
use App\Models\Currency;
use Illuminate\Support\Facades\DB;

class EnableAllCurrencies extends BaseService implements ServiceInterface
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
     * Enable all the currencies in the account.
     *
     * @param  array  $data
     * @return void
     */
    public function execute(array $data): void
    {
        $this->validateRules($data);

        DB::table('account_currencies')
            ->where('account_id', $data['account_id'])
            ->update([
                'active' => true,
            ]);
    }
}
