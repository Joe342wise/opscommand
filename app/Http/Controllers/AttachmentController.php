<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Services\FileUploadService;
use Illuminate\Http\Request;

class AttachmentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|file|max:10240',
            'entity_type' => 'required|string',
            'entity_id' => 'required|integer',
        ]);

        $attachment = FileUploadService::upload(
            $request->file('file'),
            $validated['entity_type'],
            $validated['entity_id'],
            auth()->id()
        );

        return back()->with('success', 'File uploaded successfully.');
    }

    public function destroy(Attachment $attachment)
    {
        FileUploadService::delete($attachment);

        return back()->with('success', 'File deleted successfully.');
    }

    public function download(Attachment $attachment)
    {
        return response()->download(
            storage_path("app/public/{$attachment->path}"),
            $attachment->filename
        );
    }
}
