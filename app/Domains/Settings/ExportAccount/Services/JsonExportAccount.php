<?php

namespace App\Domains\Settings\ExportAccount\Settings;

use App\Models\User;
use Illuminate\Support\Str;
use App\Services\BaseService;
use App\Models\Account;
use Illuminate\Support\Facades\Storage;
use App\ExportResources\Account\Account as AccountResource;

class JsonExportAccount extends BaseService
{
    /** @var string */
    protected $tempFileName;

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
     * Export account as Json.
     *
     * @param  array  $data
     * @return string
     */
    public function execute(array $data): string
    {
        $this->validateRules($data);

        $this->tempFileName = 'temp/'.Str::random(40).'.json';

        $this->writeExport($data, $this->author);

        return $this->tempFileName;
    }

    /**
     * Export data in temp file.
     *
     * @param  array  $data
     * @param  User  $user
     */
    private function writeExport(array $data, User $user)
    {
        $result = [];
        $result['version'] = '2.0-preview.1';
        $result['app_version'] = config('monica.app_version');
        $result['export_date'] = now();
        $result['url'] = config('app.url');
        $result['exported_by'] = $user->uuid;
        $result['account'] = $this->exportAccount($data);

        $this->writeToTempFile(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_IGNORE | JSON_UNESCAPED_SLASHES));
    }

    /**
     * Write to a temp file.
     *
     * @return void
     */
    private function writeToTempFile(string $sql)
    {
        Storage::disk('local')
            ->append($this->tempFileName, $sql);
    }

    /**
     * Export the Account table.
     *
     * @param  array  $data
     * @return mixed
     */
    private function exportAccount(array $data)
    {
        $account = Account::find($data['account_id']);

        $exporter = new AccountResource($account);

        return $exporter->resolve();
    }
}
