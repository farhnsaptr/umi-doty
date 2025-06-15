<?php

namespace App\Filament\Resources;

use App\Models\Peran;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PeranResource extends Resource
{
    protected static ?string $model = Peran::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag'; // Choose an appropriate icon for roles/tags
    protected static ?string $navigationLabel = 'Peran Pengguna'; // More descriptive label
    protected static ?string $pluralModelLabel = 'Peran Pengguna';
    protected static ?string $modelLabel = 'Peran Pengguna';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_peran')
                    ->label('Nama Peran') // Label for the input field
                    ->required()
                    ->unique(ignoreRecord: true) // Ensure role names are unique
                    ->maxLength(50),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('nama_peran')
                    ->label('Nama Peran')
                    ->searchable() // Allow searching by role name
                    ->sortable(),
            ])
            ->filters([
                // Add any filters if needed (e.g., filter by related users - less common for roles)
            ])
            ->actions([
                Tables\Actions\EditAction::make(), // Allow editing roles
                Tables\Actions\DeleteAction::make(), // Allow deleting roles
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(), // Allow deleting multiple roles
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // You could potentially add a relation manager here to list users belonging to this role
            // For example: RelationManagers\PenggunaRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\PeranResource\Pages\ListPerans::route('/'),
            'create' => \App\Filament\Resources\PeranResource\Pages\CreatePeran::route('/create'),
            'edit' => \App\Filament\Resources\PeranResource\Pages\EditPeran::route('/{record}/edit'),
        ];
    }

    // Optionally, if you want to allow global search for roles
    // public static function isGlobalSearchable(): bool
    // {
    //     return true;
    // }

    // Optionally, define which attributes are used for global search results title and details
    // protected static array $globalSearchResultAttributes = ['nama_peran'];

    // Optionally, customize the title of global search results
    // public static function getGlobalSearchResultTitle(\Illuminate\Database\Eloquent\Model $record): string
    // {
    //     return $record->nama_peran;
    // }
}