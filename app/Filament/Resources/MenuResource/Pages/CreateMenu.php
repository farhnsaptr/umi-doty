<?php

namespace App\Filament\Resources\MenuResource\Pages;

use App\Filament\Resources\MenuResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\File;

class CreateMenu extends CreateRecord
{
    protected static string $resource = MenuResource::class;

    // Remove any previous debugging or custom methods like dd()

    /**
     * Handles the creation of the record.
     * Manually processes and saves the file uploads using the relationship.
     * This is a workaround because automatic saving wasn't working reliably.
     */
    protected function handleRecordCreation(array $data): Model
    {
        Log::info('Handling Menu Creation (Manual Save)', ['data_keys' => array_keys($data)]);

        // Extract file data before creating the main record
        $filesData = $data['files'] ?? null;
        unset($data['files']); // Remove 'files' from the data array for the main record creation

        // Create the main Menu record using the rest of the data
        $menu = $this->getModel()::create($data);

        Log::info('Menu record created', ['menu_id' => $menu->id]);

        // Manually process and save the files data using the relationship
        if ($filesData && is_array($filesData)) {
            Log::info('Processing uploaded files for manual saving.');

            $diskName = 'public'; // Use the hardcoded disk name from the Resource configuration

            foreach ($filesData as $filePath) {
                 Log::info('Attempting manual File record creation for path:', ['path' => $filePath, 'disk' => $diskName]);
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
                    Log::error('Error during manual File model creation:', [
                        'error' => $e->getMessage(),
                        'filePath_from_data' => $filePath,
                        'menu_id' => $menu->id,
                        'exception_details' => [
                            'code' => $e->getCode(),
                            'file' => $e->getFile(),
                            'line' => $e->getLine(),
                        ],
                    ]);
                     \Filament\Notifications\Notification::make()
                        ->title('Error saving file')
                        ->body('Could not save database record for uploaded file.')
                        ->danger()
                        ->send();
                }
            }
        } else {
            Log::info('No file data found in form data for manual processing.');
        }

        return $menu; // Return the created Menu record
    }

    protected function getCreatedNotification(): ?\Filament\Notifications\Notification
    {
        return \Filament\Notifications\Notification::make()
            ->success()
            ->title('Menu created')
            ->body('The menu and its files were created successfully.');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}