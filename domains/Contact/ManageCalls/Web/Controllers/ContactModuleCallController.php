<?php

namespace App\Contact\ManageCalls\Web\Controllers;

use App\Contact\ManageCalls\Services\CreateCall;
use App\Contact\ManageCalls\Services\DestroyCall;
use App\Contact\ManageCalls\Services\UpdateCall;
use App\Contact\ManageCalls\Web\ViewHelpers\ModuleCallsViewHelper;
use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactModuleCallController extends Controller
{
    public function store(Request $request, int $vaultId, int $contactId)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'vault_id' => $vaultId,
            'contact_id' => $contactId,
            'called_at' => $request->input('called_at'),
            'duration' => $request->input('duration'),
            'type' => $request->input('type'),
            'answered' => $request->input('answered'),
            'who_initiated' => $request->input('who_initiated'),
        ];

        $call = (new CreateCall)->execute($data);
        $contact = Contact::find($contactId);

        return response()->json([
            'data' => ModuleCallsViewHelper::dto($contact, $call, Auth::user()),
        ], 201);
    }

    public function update(Request $request, int $vaultId, int $contactId, int $callId)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'vault_id' => $vaultId,
            'contact_id' => $contactId,
            'call_id' => $callId,
            'called_at' => $request->input('called_at'),
            'duration' => $request->input('duration'),
            'type' => $request->input('type'),
            'answered' => $request->input('answered'),
            'who_initiated' => $request->input('who_initiated'),
        ];

        $call = (new UpdateCall())->execute($data);
        $contact = Contact::find($contactId);

        return response()->json([
            'data' => ModuleCallsViewHelper::dto($contact, $call, Auth::user()),
        ], 200);
    }

    public function destroy(Request $request, int $vaultId, int $contactId, int $callId)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'vault_id' => $vaultId,
            'contact_id' => $contactId,
            'call_id' => $callId,
        ];

        (new DestroyCall())->execute($data);

        return response()->json([
            'data' => true,
        ], 200);
    }
}
