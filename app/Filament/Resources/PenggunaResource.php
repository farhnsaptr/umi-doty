<?php

namespace App\Filament\Resources;

use App\Models\Pengguna;
use App\Models\Peran;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash; // Make sure Hash facade is imported
use Illuminate\Validation\Rules\Password as PasswordRule; // Optional: For stronger password rules

class PenggunaResource extends Resource
{
    protected static ?string $model = Pengguna::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Pengguna';
    protected static ?string $pluralModelLabel = 'Pengguna';
    protected static ?string $modelLabel = 'Pengguna';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('id_peran')
                    ->label('Peran')
                    ->relationship('peran', 'nama_peran')
                    ->required()
                    ->native(false), // Optional: Use a better-looking select input

                // {{change 1}}
                // Add a comma here
                Forms\Components\TextInput::make('nama_lengkap')
                    ->required()
                    ->maxLength(100),
                // {{end change 1}}

                // {{change 2}}
                // Add a comma here
                Forms\Components\TextInput::make('username')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(50),
                // {{end change 2}}

                // {{change 3}}
                // Add a comma here
                Forms\Components\TextInput::make('email')
                    ->label('Email Address')
                    ->email() // Validate as email format
                    ->required()
                    ->unique(ignoreRecord: true) // Ensure email is unique
                    ->maxLength(255),
                // {{end change 3}}

                // {{change 4}}
                // Add a comma here
                // CORRECTED Password Field Handling: Use the actual column name 'password_hash'
                Forms\Components\TextInput::make('password_hash') // USE THE ACTUAL COLUMN NAME
                    ->label('Password') // Use a user-friendly label
                    ->password()
                    ->revealable()
                    ->maxLength(255)
                    // Required only on creation
                    ->required(fn (string $operation): bool => $operation === 'create')
                    // Optional: Add strong password rules
                    // ->rules([PasswordRule::defaults()]),
                    // Hash the input value from this field
                    ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                    // Do NOT dehydrate (save) the field if it was left empty during edit.
                    // This prevents overwriting the existing hashed password.
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    // Hide the field on the view page
                    ->hiddenOn('view'),
                // {{end change 4}}

                // No comma needed after the last item in the array
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id') // Often good to include ID
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('peran.nama_peran')
                    ->label('Peran')
                    ->sortable(),
                Tables\Columns\TextColumn::make('nama_lengkap')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('username')
                    ->label('Username')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                // Avoid showing password_hash in the table for security
            ])
            ->filters([
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
            //
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

    // Optional: Enable global search for this resource
    // public static function isGlobalSearchable(): bool
    // {
    //     return true;
    // }

    // Optional: Define attributes for global search results
    // protected static array $globalSearchResultAttributes = ['nama_lengkap', 'username', 'email'];

    // Optional: Customize global search result title
    // public static function getGlobalSearchResultTitle(\Illuminate\Database\Eloquent\Model $record): string
    // {
    //     return $record->nama_lengkap;
    // }
}