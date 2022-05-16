<?php

namespace App\Contact\ManageTasks\Web\ViewHelpers;

use App\Helpers\DateHelper;
use App\Models\Contact;
use App\Models\ContactTask;
use App\Models\User;

class ModuleContactTasksViewHelper
{
    public static function data(Contact $contact, User $user): array
    {
        $tasks = $contact->tasks()
            ->orderBy('id', 'desc')
            ->get();

        $tasksCollection = $tasks->map(function ($task) use ($contact, $user) {
            return self::dtoTask($contact, $task, $user);
        });

        return [
            'tasks' => $tasksCollection,
            'url' => [
                'store' => route('contact.task.store', [
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
                'update' => route('contact.task.update', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                    'task' => $task->id,
                ]),
                'destroy' => route('contact.task.destroy', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                    'task' => $task->id,
                ]),
            ],
        ];
    }
}
