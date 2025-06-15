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

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationLabel = 'Peran Pengguna';
    protected static ?string $pluralModelLabel = 'Peran Pengguna';
    protected static ?string $modelLabel = 'Peran Pengguna';
    // {{change 1}}
    protected static ?string $navigationGroup = 'Manajemen User'; // Assign to 'Manajemen User' group
    protected static ?int $navigationSort = 2; // Optional: Order within the group (e.g., Peran second)
    // {{end change 1}}

    public static function form(Form $form): Form
    {
        return $form
                ->schema([
                    Forms\Components\TextInput::make('nama_peran')
                        ->label('Nama Peran')
                        ->required()
                        ->unique(ignoreRecord: true)
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
            'index' => \App\Filament\Resources\PeranResource\Pages\ListPerans::route('/'),
            'create' => \App\Filament\Resources\PeranResource\Pages\CreatePeran::route('/create'),
            'edit' => \App\Filament\Resources\PeranResource\Pages\EditPeran::route('/{record}/edit'),
        ];
    }
}