<?php

namespace App\Contact\ManageReminders\Web\ViewHelpers;

use App\Models\User;
use App\Models\Contact;
use App\Helpers\DateHelper;
use App\Models\ContactReminder;
use App\Helpers\ContactReminderHelper;
use App\Models\ContactTask;

class ModuleContactTasksViewHelper
{
    public static function data(Contact $contact, User $user): array
    {
        $tasks = $contact->tasks()
            ->orderBy('id')
            ->get();

        $tasksCollection = $tasks->map(function ($task) use ($contact, $user) {
            return self::dtoTask($contact, $task, $user);
        });

        return [
            'tasks' => $tasksCollection,
            'url' => [
                'store' => route('contact.reminder.store', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                ]),
            ],
        ];
    }

    public static function dtoTask(Contact $contact, ContactTask $task, User $user): array
    {
        return [
            'id' => $task->id,
            'label' => $task->label,
            'description' => $task->description,
            'completed' => $task->completed,
            'completed_at' => $task->completed_at ? DateHelper::format($task->completed_at, $user) : null,
            'url' => [
                'update' => route('contact.reminder.update', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                    'reminder' => $task->id,
                ]),
                'destroy' => route('contact.reminder.destroy', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                    'reminder' => $task->id,
                ]),
            ],
        ];
    }
}
