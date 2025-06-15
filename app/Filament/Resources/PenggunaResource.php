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
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password as PasswordRule;

class PenggunaResource extends Resource
{
    protected static ?string $model = Pengguna::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Pengguna';
    protected static ?string $pluralModelLabel = 'Pengguna';
    protected static ?string $modelLabel = 'Pengguna';
    // {{change 1}}
    protected static ?string $navigationGroup = 'Manajemen User'; // Assign to 'Manajemen User' group
    protected static ?int $navigationSort = 1; // Optional: Order within the group (e.g., Pengguna first)
    // {{end change 1}}

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('id_peran')
                    ->label('Peran')
                    ->relationship('peran', 'nama_peran')
                    ->required()
                    ->native(false),

                Forms\Components\TextInput::make('nama_lengkap')
                    ->required()
                    ->maxLength(100),

                Forms\Components\TextInput::make('username')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(50),

                Forms\Components\TextInput::make('email')
                    ->label('Email Address')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),

                Forms\Components\TextInput::make('password_hash')
                    ->label('Password')
                    ->password()
                    ->revealable()
                    ->maxLength(255)
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->hiddenOn('view'),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
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
}