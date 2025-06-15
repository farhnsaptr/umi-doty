<?php

namespace App\Filament\Resources\PesananResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DetailPesananRelationManager extends RelationManager
{
    protected static string $relationship = 'detailPesanan'; // Must match relationship method in Pesanan model

    protected static ?string $title = 'Detail Pesanan'; // Title for the section

    // No form method needed if you only want to view details, not edit them here
    // public function form(Form $form): Form
    // {
    //     return $form
    //         ->schema([
    //             // Define fields here if you want to edit order items individually
    //         ]);
    // }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id') // Placeholder, will be overridden by columns below
            ->columns([
                Tables\Columns\TextColumn::make('menu.nama_menu') // Display menu name from relationship
                    ->label('Menu'),

                Tables\Columns\TextColumn::make('varianMenu.nama_varian') // Display variant name if exists
                    ->label('Varian')
                    ->default('-'), // Show '-' if no variant

                Tables\Columns\TextColumn::make('jumlah')
                    ->label('Jumlah')
                    ->numeric(),

                Tables\Columns\TextColumn::make('harga_saat_pesan')
                    ->label('Harga Satuan')
                    ->numeric()
                    ->money('IDR'), // Format as currency

                Tables\Columns\TextColumn::make('catatan_custom')
                    ->label('Catatan')
                    ->limit(50) // Limit display length
                    ->toggleable(isToggledHiddenByDefault: true), // Hidden by default

                Tables\Columns\TextColumn::make('subtotal') // Calculate subtotal on the fly
                    ->label('Subtotal')
                     ->getStateUsing(fn ($record): string => number_format($record->jumlah * $record->harga_saat_pesan, 2, ',', '.'))
                    ->money('IDR'), // Format as currency

            ])
            ->filters([
                //
            ])
            // Remove header actions like Create if you don't want to create items here
            // ->headerActions([
            //     Tables\Actions\CreateAction::make(),
            // ])
            ->actions([
                // Remove actions like Edit, Delete if you don't want to modify items here
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                 // Remove bulk actions if you don't want to modify items here
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }
}