<?php

namespace App\Services;

use App\Models\Attachment;
use App\Models\Logbook;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class AttachmentService
{
    public function storeUploadedPhoto(Logbook $logbook, UploadedFile $file, ?string $caption = null, int $order = 0): Attachment
    {
        $ext = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        $uuid = (string) Str::uuid();
        $dir = "attachments/{$logbook->internship_id}/" . $logbook->date->format('Y-m');
        $filename = "{$uuid}.{$ext}";

        $path = $file->storeAs($dir, $filename, 'public');
        $sizeKb = (int) round($file->getSize() / 1024);

        return Attachment::create([
            'logbook_id' => $logbook->id,
            'path' => $path,
            'thumb_path' => $path,
            'mime' => $file->getMimeType() ?: 'image/jpeg',
            'size_kb' => max(1, $sizeKb),
            'caption' => $caption ?: $logbook->title,
            'taken_at' => now(),
            'sort_order' => $order,
        ]);
    }
}
