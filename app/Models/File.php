<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log; // Import Log facade

class File extends Model
{
    use HasFactory;

    protected $table = 'files';
    // Your SQL shows created_at and updated_at columns
    public $timestamps = true;

    // These fields MUST be fillable for Filament's FileUpload to save related records.
    protected $fillable = [
        'fileable_id',
        'fileable_type',
        'filename',
        'path',
        'mime_type',
        'size',
        // Add any other columns you added if they need mass assignment
    ];

    protected $casts = [
        // Cast timestamps if needed (usually handled by Laravel)
        // 'created_at' => 'datetime',
        // 'updated_at' => 'datetime',
        // 'size' => 'integer', // Optional: Cast size
    ];

    /**
     * Get the parent model that owns the file.
     * MorphTo relationship: this File model belongs to a parent that is 'fileable'.
     * 'fileable' MUST match the morphs() call in the migration and morphMany() in related models.
     */
    public function fileable(): MorphTo
    {
        return $this->morphTo('fileable'); // Explicitly define the morph name
    }

    /**
     * The "booted" method of the model.
     * Add event listeners for debugging and file deletion.
     */
    protected static function booted(): void
    {
        // {{change 1}}
        // Debugging: Log when a File model is about to be created
        static::creating(function (File $file) {
            Log::info('File Model Creating Event Triggered', [
                'attributes' => $file->getAttributes(),
                'isDirty' => $file->isDirty(),
                'fillable' => $file->getFillable(),
                'connection' => $file->getConnectionName(), // Log the database connection being used
            ]);
        });
         // {{end change 1}}

        // Listener to delete physical file when the database record is deleted
        static::deleting(function (File $file) {
            Log::info('File Model Deleting Event Triggered', ['path' => $file->path]);
            $diskName = 'public'; // Assuming 'public' disk is used for storage
            if (Storage::disk($diskName)->exists($file->path)) {
                 $success = Storage::disk($diskName)->delete($file->path);
                 Log::info('Physical file deletion result:', ['success' => $success, 'path' => $file->path]);
            } else {
                 Log::warning('Physical file not found for deletion:', ['path' => $file->path]);
            }
        });
    }
}