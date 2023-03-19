<?php

namespace App\Domains\Settings\ExportAccount\Web\ViewHelpers;

use App\Helpers\DateHelper;
use App\Models\Account;
use App\Models\ExportJob;

class ExportAccountViewHelper
{
    public static function data(Account $account): array
    {
        return [
            'url' => [
                'settings' => route('settings.index'),
                'back' => route('settings.index'),
                'destroy' => route('settings.cancel.destroy'),
            ],
            'jobs' => $account->exportJobs()->orderBy('created_at', 'desc')->get()->map(fn ($job) => static::dtoJob($job)),
        ];
    }

    public static function dtoJob(ExportJob $job): array
    {
        $status = '';
        switch ($job->status) {
            case ExportJob::EXPORT_TODO:
                $status = __('Todo');
                break;
            case ExportJob::EXPORT_DOING:
                $status = __('In progress');
                break;
            case ExportJob::EXPORT_DONE:
                $status = __('Done');
                break;
            case ExportJob::EXPORT_FAILED:
                $status = __('Failed');
                break;
        }

        return [
            'id' => $job->id,
            'date' => DateHelper::formatShortDateWithTime($job->created_at),
            'status' => $status,
        ];
    }
}
