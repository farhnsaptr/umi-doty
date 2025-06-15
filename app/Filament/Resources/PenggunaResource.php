<?php

namespace App\Filament\Resources;

use App\Models\Pengguna;
use App\Models\Peran; // Import Peran model for the relationship field
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash; // Import Hash facade

class PenggunaResource extends Resource
{
    protected static ?string $model = Pengguna::class;

    protected static ?string $navigationIcon = 'heroicon-o-users'; // Choose an appropriate icon
    protected static ?string $navigationLabel = 'Pengguna';
    protected static ?string $pluralModelLabel = 'Pengguna';
    protected static ?string $modelLabel = 'Pengguna';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('id_peran')
                    ->label('Peran') // Label for the dropdown
                    ->relationship('peran', 'nama_peran') // Relate to Peran model, show nama_peran
                    ->required(),
                Forms\Components\TextInput::make('nama_lengkap')
                    ->required()
                    ->maxLength(100),
                Forms\Components\TextInput::make('username')
                    ->required()
                    ->unique(ignoreRecord: true) // Ensure username is unique, ignore current record on edit
                    ->maxLength(50),
                Forms\Components\TextInput::make('password') // Use a field named 'password' for input
                    ->label('Password')
                    ->password() // Make it a password field
                    ->revealable()
                    ->maxLength(255)
                    ->required(fn (string $operation): bool => $operation === 'create') // Required only on create
                    ->dehydrateStateUsing(fn (string $state): string => Hash::make($state)) // Hash the password
                    ->dehydrated(fn (?string $state): bool => filled($state)) // Only save if the field is filled
                    ->hiddenOn('view'), // Hide password on view page
                Forms\Components\TextInput::make('password_hash') // Keep password_hash internally
                    ->hidden()
                    ->dehydrated(true),
                // Note: We are handling password hashing and using 'password' for the form input
                // but mapping it to 'password_hash' in the model/database. Filament handles this
                // using dehydrateStateUsing.
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('peran.nama_peran') // Display role name from relationship
                    ->label('Peran')
                    ->sortable(),
                Tables\Columns\TextColumn::make('nama_lengkap')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('username')
                    ->searchable()
                    ->sortable(),
                // Avoid showing password_hash in the table
                // Tables\Columns\TextColumn::make('password_hash'),
            ])
            ->filters([
                // Add filters if needed, e.g., by role
                Tables\Filters\SelectFilter::make('id_peran')
                    ->label('Filter by Peran')
                    ->relationship('peran', 'nama_peran'),
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
            // Define any related resources here, e.g., Pesanan related to Pengguna
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\PenggunaResource\Pages\ListPenggunas::route('/'),
            'create' => \App\Filament\Resources\PenggunaResource\Pages\CreatePengguna::route('/create'),
            'edit' => \App\Filament\Resources\PenggunaResource\Pages\EditPengguna::route('/{record}/edit'),
        ];
    }

    // You might need to add methods here for authorization if roles control access
    // For example: canViewAny(), canCreate(), canEdit(), canDelete()
}