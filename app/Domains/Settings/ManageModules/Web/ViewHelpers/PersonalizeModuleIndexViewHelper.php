<?php

namespace App\Domains\Settings\ManageModules\Web\ViewHelpers;

use App\Models\Account;
use App\Models\Module;

class PersonalizeModuleIndexViewHelper
{
    public static function data(Account $account): array
    {
        $modules = $account->modules()
            ->where('can_be_deleted', true)
            ->orderBy('name', 'asc')
            ->get();

        $collection = collect();
        foreach ($modules as $module) {
            $collection->push(self::dtoModule($module));
        }

        return [
            'modules' => $collection,
            'url' => [
                'settings' => route('settings.index'),
                'personalize' => route('settings.personalize.index'),
                'create' => route('settings.personalize.module.create'),
            ],
        ];
    }

    public static function dtoModule(Module $module): array
    {
        return [
            'id' => $module->id,
            'name' => $module->name,
            'type' => $module->type,
            'reserved_to_contact_information' => $module->reserved_to_contact_information,
            'can_be_deleted' => $module->can_be_deleted,
            'url' => [
                'show' => route('settings.personalize.module.show', [
                    'module' => $module->id,
                ]),
                'update' => route('settings.personalize.module.update', [
                    'module' => $module->id,
                ]),
                'destroy' => route('settings.personalize.module.destroy', [
                    'module' => $module->id,
                ]),
            ],
        ];
    }
}
