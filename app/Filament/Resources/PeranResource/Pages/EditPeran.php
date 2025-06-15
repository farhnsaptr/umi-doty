<?php

namespace App\Filament\Resources\PeranResource\Pages;

use App\Filament\Resources\PeranResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPeran extends EditRecord
{
    protected static string $resource = PeranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
