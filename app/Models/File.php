<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class File extends Model
{
    use HasFactory;

    protected $table = 'files';
    public $timestamps = true;

    protected $fillable = [
        'fileable_id',
        'fileable_type',
        'filename',
        'path',
        'mime_type',
        'size',
    ];

    protected $casts = [
        // 'size' => 'integer',
        // 'created_at' => 'datetime',
        // 'updated_at' => 'datetime',
    ];

    public function fileable(): MorphTo
    {
        return $this->morphTo('fileable');
    }

    protected static function booted(): void
    {
        static::creating(function (File $file) {
            Log::info('File Model Creating Event Triggered', [
                'attributes' => $file->getAttributes(),
                'isDirty' => $file->isDirty(),
                'fillable' => $file->getFillable(),
                'connection' => $file->getConnectionName(),
            ]);
        });

        static::deleting(function (File $file) {
            Log::info('File Model Deleting Event Triggered', ['path' => $file->path]);
            $diskName = 'public';
            if ($file->path && Storage::disk($diskName)->exists($file->path)) {
                 $success = Storage::disk($diskName)->delete($file->path);
                 Log::info('Physical file deletion result:', ['success' => $success, 'path' => $file->path]);
            } else {
                 Log::warning('Physical file not found for deletion:', ['path' => $file->path]);
            }
        });
    }
}