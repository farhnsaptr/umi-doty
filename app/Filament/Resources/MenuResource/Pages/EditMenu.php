<?php

namespace App\Filament\Resources\MenuResource\Pages;

use App\Filament\Resources\MenuResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\File;
use Illuminate\Support\Collection;

class EditMenu extends EditRecord
{
    protected static string $resource = MenuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * Mutate the data before the form is filled on the edit page.
     * Ensures the 'files' relationship data is loaded and formatted
     * as an array of paths for the FileUpload component's internal processing.
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        Log::info('Mutating data before filling EditMenu form.', ['initial_data_keys' => array_keys($data)]);

        // Get the Menu record being edited
        $menu = $this->getRecord();

        // Load the 'files' relationship
        $menu->load('files');

        // {{change 1}}
        // Extract only the 'path' from each related File model and put it into an array.
        // This array of paths is what the FileUpload component expects for existing files
        // for its internal processing and display.
        $data['files'] = $menu->files->pluck('path')->toArray();
        // {{end change 1}}


        Log::info('Data mutated before filling EditMenu form:', [
            'menu_id' => $menu->id,
            'files_count_in_relation' => $menu->files->count(),
            'files_data_in_form_state' => $data['files'], // Log the array of paths
            'final_data_keys' => array_keys($data),
        ]);

        return $data;
    }


    /**
     * Handles the update of the record.
     * Manually processes and saves (adds/removes) the file uploads using the relationship.
     * This is a workaround because automatic saving wasn't working reliably.
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $menu = $record;

        Log::info('Handling Menu Update (Manual Save)', ['menu_id' => $menu->id, 'data_keys' => array_keys($data)]);

        // The FileUpload component for a relationship returns an array of *current* file representations
        // when the form is submitted. This array contains data for both existing files and newly uploaded ones.
        // Each item in the array is either:
        // 1. A string (path for a newly uploaded file before save)
        // 2. An array representation of an existing File model (when editing, this comes from the form state)
        // 3. A File model instance (less common)
        $currentFilesData = $data['files'] ?? [];
        unset($data['files']); // Remove 'files' from the data array for the main record update

        // Update the main Menu record with the data excluding files
        $menu->update($data);

        Log::info('Menu record updated', ['menu_id' => $menu->id]);

        // Manually synchronize the File model records based on the submitted files data
        $diskName = 'public'; // Use the hardcoded disk name

        // Get the paths of files currently in the form state from the submitted data
        // The submitted data might contain strings (new uploads) or arrays/models (existing files).
        // We need to map them all to just the path string.
        $submittedFilePaths = collect($currentFilesData)
             ->map(function ($file) {
                if (is_string($file)) {
                    return $file; // New upload path
                } elseif (is_array($file) && isset($file['path'])) {
                    return $file['path']; // Existing file represented as array
                } elseif ($file instanceof \Illuminate\Database\Eloquent\Model && isset($file->path)) {
                     return $file->path; // Existing file as Model instance
                }
                 return null;
             })
             ->filter()
             ->toArray();

        Log::info('Submitted file paths from form state:', ['paths' => $submittedFilePaths]);

        // Get the paths of files currently associated with this Menu in the database
        $existingFilePaths = $menu->files()->pluck('path')->toArray(); // Use relationship query builder

        Log::info('Existing file paths in database:', ['paths' => $existingFilePaths]);

        // Files to add: paths in submitted data but not in database
        $filesToAddPaths = array_diff($submittedFilePaths, $existingFilePaths);

        // Files to remove: paths in database but not in submitted data
        $filesToRemovePaths = array_diff($existingFilePaths, $submittedFilePaths);

        Log::info('Files to add:', ['paths' => $filesToAddPaths]);
        Log::info('Files to remove:', ['paths' => $filesToRemovePaths]);


        // Add new files to the database
        foreach ($filesToAddPaths as $filePath) {
             Log::info('Adding new File record for path:', ['path' => $filePath]);
            try {
                 $fullDiskPath = $filePath; // Path is relative to disk/directory

                if (Storage::disk($diskName)->exists($fullDiskPath)) {
                    $fileName = basename($fullDiskPath);
                    $mimeType = Storage::disk($diskName)->mimeType($fullDiskPath);
                    $fileSize = Storage::disk($diskName)->size($fullDiskPath);

                     Log::info('Creating File record with attributes:', [
                         'filename' => $fileName,
                        'path' => $fullDiskPath,
                        'mime_type' => $mimeType,
                        'size' => $fileSize,
                        'fileable_id' => $menu->id,
                        'fileable_type' => get_class($menu),
                     ]);

                    $menu->files()->create([
                        'filename' => $fileName,
                        'path' => $fullDiskPath,
                        'mime_type' => $mimeType,
                        'size' => $fileSize,
                    ]);
                    Log::info('File model record created successfully manually for path:', ['path' => $fullDiskPath]);
                } else {
                    Log::warning('Physical file not found on disk for metadata extraction and saving:', ['path' => $fullDiskPath, 'disk' => $diskName]);
                }

            } catch (\Exception $e) {
                Log::error('Error adding File model record during update:', [
                    'error' => $e->getMessage(),
                    'filePath_to_add' => $filePath,
                    'menu_id' => $menu->id,
                    'exception_details' => [
                        'code' => $e->getCode(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                    ],
                ]);
                 \Filament\Notifications\Notification::make()
                    ->title('Error saving file')
                    ->body('Could not save database record for added file.')
                    ->danger()
                    ->send();
            }
        }

        // Remove files from the database (and storage via model's deleting event)
        if (!empty($filesToRemovePaths)) {
             Log::info('Removing File records for paths:', ['paths' => $filesToRemovePaths]);
             try {
                File::whereIn('path', $filesToRemovePaths)
                    ->where('fileable_id', $menu->id)
                    ->where('fileable_type', get_class($menu))
                    ->get()
                    ->each(function ($fileModel) {
                        $fileModel->delete(); // Delete each model, triggering the booted() deletion logic
                    });
                 Log::info('Attempted to remove File records.');
             } catch (\Exception $e) {
                 Log::error('Error removing File model records during update:', [
                    'error' => $e->getMessage(),
                    'paths_to_remove' => $filesToRemovePaths,
                    'menu_id' => $menu->id,
                    'exception_details' => [
                        'code' => $e->getCode(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                    ],
                 ]);
                  \Filament\Notifications\Notification::make()
                    ->title('Error removing file')
                    ->body('Could not remove database record for file.')
                    ->danger()
                    ->send();
             }
        }

        return $menu;
    }

    protected function getSavedNotification(): ?\Filament\Notifications\Notification
    {
        return \Filament\Notifications\Notification::make()
            ->success()
            ->title('Menu updated')
            ->body('The menu and its files were updated successfully.');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}