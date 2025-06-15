<?php

namespace App\Filament\Resources;

use App\Models\KategoriMenu;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class KategoriMenuResource extends Resource
{
    protected static ?string $model = KategoriMenu::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag'; // Using a tag icon, feel free to change
    protected static ?string $navigationLabel = 'Kategori Menu';
    protected static ?string $pluralModelLabel = 'Kategori Menu';
    protected static ?string $modelLabel = 'Kategori Menu';
    protected static ?int $navigationSort = 2; // Optional: Define order in sidebar

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_kategori')
                    ->label('Nama Kategori')
                    ->required()
                    ->unique(ignoreRecord: true) // Ensure category names are unique
                    ->maxLength(100),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('nama_kategori')
                    ->label('Nama Kategori')
                    ->searchable() // Allow searching by category name
                    ->sortable(),
            ])
            ->filters([
                // Add filters if needed
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // You can add a relation manager here to list Menu items
            // belonging to this category. You would first need to create
            // a MenuRelationManager.
            // RelationManagers\MenuRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\KategoriMenuResource\Pages\ListKategoriMenus::route('/'),
            'create' => \App\Filament\Resources\KategoriMenuResource\Pages\CreateKategoriMenu::route('/create'),
            'edit' => \App\Filament\Resources\KategoriMenuResource\Pages\EditKategoriMenu::route('/{record}/edit'),
        ];
    }

    // Optional: Enable global search for this resource
    // public static function isGlobalSearchable(): bool
    // {
    //     return true;
    // }

    // Optional: Define attributes for global search results
    // protected static array $globalSearchResultAttributes = ['nama_kategori'];

    // Optional: Customize global search result title
    // public static function getGlobalSearchResultTitle(\Illuminate\Database\Eloquent\Model $record): string
    // {
    //     return $record->nama_kategori;
    // }
}