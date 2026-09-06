<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A file uploaded on a reply. `disk` is always "local" (private) — files are
 * never stored on the public disk, so they can only be reached through
 * ConversationController::downloadAttachment(), which checks authorization
 * before streaming the file.
 */
class Attachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'reponse_id',
        'disk',
        'path',
        'original_name',
        'mime_type',
        'size',
    ];

    public function reponse(): BelongsTo
    {
        return $this->belongsTo(Reponse::class);
    }

    /** Formats the byte size for display (e.g. "1.4 Mo") instead of a raw byte count. */
    public function humanSize(): string
    {
        $size = $this->size;

        foreach (['o', 'Ko', 'Mo', 'Go'] as $unit) {
            if ($size < 1024) {
                return round($size, 1).' '.$unit;
            }
            $size /= 1024;
        }

        return round($size, 1).' To';
    }
}
