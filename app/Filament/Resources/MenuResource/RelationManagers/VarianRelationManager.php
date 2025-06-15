<?php

namespace App\Filament\Resources\MenuResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VarianRelationManager extends RelationManager
{
    protected static string $relationship = 'varian'; // Ensure this matches the relationship method name in your Menu model

    // Optional: Set a title for the relation manager section
    protected static ?string $title = 'Varian Menu';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_varian')
                    ->label('Nama Varian')
                    ->required()
                    ->maxLength(50), // Max length as per your schema

                Forms\Components\TextInput::make('harga')
                    ->label('Harga Varian')
                    ->required()
                    ->numeric() // Ensure numeric input
                    ->prefix('Rp'), // Add currency prefix (adjust currency code if needed)
                    // ->live() // Optional: if you need live updates
                    // ->debounce(500), // Optional: debounce live updates

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama_varian') // This should match the third argument of the artisan command
            ->columns([
                Tables\Columns\TextColumn::make('nama_varian')
                    ->label('Nama Varian')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('harga')
                    ->label('Harga')
                    ->numeric()
                    ->sortable()
                    ->money('IDR'), // Format as Indonesian Rupiah (adjust currency code if needed)

            ])
            ->filters([
                // Add filters if needed
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(), // Allow creating new variants
            ])
            ->actions([
                Tables\Actions\EditAction::make(), // Allow editing variants
                Tables\Actions\DeleteAction::make(), // Allow deleting variants
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(), // Allow bulk deleting variants
                ]),
            ]);
    }
}