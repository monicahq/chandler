<?php

namespace App\Domains\Settings\ExportAccount\Services;

use App\ExportResources\Account\Account as AccountResource;
use App\Models\Account;
use App\Models\User;
use App\Services\BaseService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class JsonExportAccount extends BaseService
{
    /** @var string */
    protected $tempFileName;

    /**
     * Get the validation rules that apply to the service.
     */
    public function rules(): array
    {
        return [
            'account_id' => 'required|uuid|exists:accounts,id',
            'author_id' => 'required|uuid|exists:users,id',
        ];
    }

    /**
     * Get the permissions that apply to the user calling the service.
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
     */
    public function execute(array $data): string
    {
        $this->validateRules($data);

        $this->tempFileName = 'temp/'.Str::uuid().'.json';

        $this->writeExport($data, $this->author);

        return $this->tempFileName;
    }

    /**
     * Export data in temp file.
     */
    private function writeExport(array $data, User $user)
    {
        $result = [];
        $result['version'] = '2.0-preview.1';
        $result['app_version'] = config('monica.app_version');
        $result['export_date'] = now();
        $result['url'] = config('app.url');
        $result['exported_by'] = $user->id;
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
     * @return mixed
     */
    private function exportAccount(array $data)
    {
        $account = Account::find($data['account_id']);

        $exporter = new AccountResource($account);

        return $exporter->resolve();
    }
}
