<?php

namespace App\Services;

use App\Models\Attachment;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileUploadService
{
    public static function upload(UploadedFile $file, string $entityType, int $entityId, ?int $uploadedBy = null): Attachment
    {
        $path = $file->store("attachments/{$entityType}/{$entityId}", 'public');

        return Attachment::create([
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'filename' => $file->getClientOriginalName(),
            'path' => $path,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'uploaded_by' => $uploadedBy ?? auth()->id(),
        ]);
    }

    public static function delete(Attachment $attachment): bool
    {
        Storage::disk('public')->delete($attachment->path);

        return $attachment->delete();
    }

    public static function getAttachments(string $entityType, int $entityId)
    {
        return Attachment::where('entity_type', $entityType)
            ->where('entity_id', $entityId)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
