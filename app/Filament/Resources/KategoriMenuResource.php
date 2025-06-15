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

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationLabel = 'Kategori Menu';
    protected static ?string $pluralModelLabel = 'Kategori Menu';
    protected static ?string $modelLabel = 'Kategori Menu';
    // {{change 1}}
    protected static ?string $navigationGroup = 'Manajemen Menu'; // Assign to 'Manajemen Menu' group
    protected static ?int $navigationSort = 1; // Optional: Order within the group (e.g., Kategori first)
    // {{end change 1}}


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_kategori')
                    ->label('Nama Kategori')
                    ->required()
                    ->unique(ignoreRecord: true)
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
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
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
            //
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
}