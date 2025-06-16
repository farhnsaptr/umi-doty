<?php

namespace App\Filament\Resources\MenuResource\Pages;

use App\Filament\Resources\MenuResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder; // Import Builder

class ListMenus extends ListRecords
{
    protected static string $resource = MenuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    /**
     * Get the query for the table.
     * Eager load the 'files' relationship to display images in the table column.
     */
    protected function getTableQuery(): Builder
    {
        // Get the default query builder for the Menu model
        $query = parent::getTableQuery();

        // Eager load the 'files' relationship.
        // This is necessary for the ImageColumn::make('files.0.path') to work efficiently.
        $query->with('files');

        return $query;
    }
}