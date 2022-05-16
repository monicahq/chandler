<?php

namespace App\Contact\ManageTasks\Web\Controllers;

use App\Contact\ManageReminders\Services\CreateContactReminder;
use App\Contact\ManageReminders\Services\DestroyReminder;
use App\Contact\ManageReminders\Services\UpdateReminder;
use App\Contact\ManageReminders\Web\ViewHelpers\ModuleRemindersViewHelper;
use App\Contact\ManageTasks\Services\CreateContactTask;
use App\Contact\ManageTasks\Services\UpdateContactTask;
use App\Contact\ManageTasks\Web\ViewHelpers\ModuleContactTasksViewHelper;
use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\ContactReminder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactModuleTaskController extends Controller
{
    public function store(Request $request, int $vaultId, int $contactId)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'vault_id' => $vaultId,
            'contact_id' => $contactId,
            'label' => $request->input('label'),
            'description' => null,
        ];

        $task = (new CreateContactTask)->execute($data);
        $contact = Contact::find($contactId);

        return response()->json([
            'data' => ModuleContactTasksViewHelper::dtoTask($contact, $task, Auth::user()),
        ], 201);
    }

    public function update(Request $request, int $vaultId, int $contactId, int $taskId)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'vault_id' => $vaultId,
            'contact_id' => $contactId,
            'contact_task_id' => $taskId,
            'label' => $request->input('label'),
            'description' => null,
        ];

        $task = (new UpdateContactTask)->execute($data);
        $contact = Contact::find($contactId);

        return response()->json([
            'data' => ModuleContactTasksViewHelper::dtoTask($contact, $task, Auth::user()),
        ], 200);
    }

    public function destroy(Request $request, int $vaultId, int $contactId, int $taskId)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'vault_id' => $vaultId,
            'contact_id' => $contactId,
            'contact_reminder_id' => $taskId,
        ];

        (new DestroyReminder)->execute($data);

        return response()->json([
            'data' => true,
        ], 200);
    }
}
