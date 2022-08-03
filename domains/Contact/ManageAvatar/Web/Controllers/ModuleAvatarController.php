<?php

namespace App\Contact\ManageAvatar\Web\Controllers;

use App\Contact\ManageAvatar\Services\UpdatePhotoAsAvatar;
use App\Contact\ManageCalls\Services\DestroyCall;
use App\Contact\ManageCalls\Web\ViewHelpers\ModuleCallsViewHelper;
use App\Contact\ManageDocuments\Services\UploadFile;
use App\Http\Controllers\Controller;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ModuleAvatarController extends Controller
{
    public function update(Request $request, int $vaultId, int $contactId)
    {
        // first we upload the file
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'vault_id' => $vaultId,
            'contact_id' => $contactId,
            'uuid' => $request->input('uuid'),
            'name' => $request->input('name'),
            'original_url' => $request->input('original_url'),
            'cdn_url' => $request->input('cdn_url'),
            'mime_type' => $request->input('mime_type'),
            'size' => $request->input('size'),
            'type' => File::TYPE_AVATAR,
        ];

        $file = (new UploadFile())->execute($data);

        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'vault_id' => $vaultId,
            'contact_id' => $contactId,
            'file_id' => $file->id,
        ];

        $call = (new UpdatePhotoAsAvatar())->execute($data);

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
