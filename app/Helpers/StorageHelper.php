<?php

namespace App\Helpers;

use App\Models\Account;
use App\Models\File;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;

class StorageHelper
{
    /**
     * Get a filesystem instance.
     *
     * @param  string  $name
     * @return \Illuminate\Filesystem\FilesystemAdapter
     */
    public static function disk($name = null): FilesystemAdapter
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter */
        $disk = Storage::disk($name);

        return $disk;
    }

    /**
     * Indicate if the account can accept another file, depending on the account's
     * limits.
     *
     * @param  Account  $account
     * @return bool
     */
    public static function canUploadFile(Account $account): bool
    {
        // get the file size of all the files in the account
        // the size will be in bytes
        $vaultIds = $account->vaults()->select('id')->get()->toArray();

        $totalSizeInBytes = File::whereIn('vault_id', $vaultIds)->sum('size');

        $accountLimit = $account->storage_limit_in_mb * 1024 * 1024;

        return $totalSizeInBytes < $accountLimit;
    }
}
