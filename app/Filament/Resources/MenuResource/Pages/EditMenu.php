<?php

namespace App\Filament\Resources\MenuResource\Pages;

use App\Filament\Resources\MenuResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
// Removed unused imports like Log

class EditMenu extends EditRecord
{
    protected static string $resource = MenuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    // Removed mutateFormDataBeforeFill and mutateFormDataBeforeSave dd()
    // as they were for debugging. Filament's standard saving should handle the form data.

    // protected function mutateFormDataBeforeFill(array $data): array { return $data; }
    // protected function mutateFormDataBeforeSave(array $data): array { return $data; }

    protected function getRedirectUrl(): string
    {
         // Redirect to the index page after saving
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Menu saved successfully.';
    }
}