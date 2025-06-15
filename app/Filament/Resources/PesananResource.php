<?php

namespace App\Filament\Resources;

use App\Models\Pesanan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PesananResource\RelationManagers; // Import RelationManagers namespace
use App\Filament\Resources\PesananResource\Pages;


class PesananResource extends Resource
{
    protected static ?string $model = Pesanan::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text'; // Using a document icon
    protected static ?string $navigationLabel = 'Pesanan';
    protected static ?string $pluralModelLabel = 'Pesanan';
    protected static ?string $modelLabel = 'Pesanan';
    protected static ?int $navigationSort = 1; // Optional: Order in sidebar group
    // {{change 1}}
    protected static ?string $navigationGroup = 'Penjualan'; // Assign to a new group 'Penjualan'
    // {{end change 1}}


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(2) // Use a grid for layout
                    ->schema([
                        Forms\Components\Select::make('id_kasir')
                            ->label('Kasir')
                            ->relationship('kasir', 'nama_lengkap') // Display kasir's name
                            ->disabled() // Make this field read-only
                            ->dehydrated(false), // Do not include in form data for saving

                        Forms\Components\Select::make('id_pelanggan')
                            ->label('Pelanggan')
                            ->relationship('pelanggan', 'nama_pelanggan') // Display customer's name
                            ->disabled() // Make this field read-only
                            ->dehydrated(false)
                            ->nullable(), // Allow null if no customer

                        Forms\Components\TextInput::make('nomor_struk')
                            ->label('Nomor Struk')
                            ->disabled() // Make this field read-only
                            ->dehydrated(false),

                        Forms\Components\DateTimePicker::make('waktu_pesanan')
                            ->label('Waktu Pesanan')
                            ->disabled() // Make this field read-only
                            ->dehydrated(false),

                        Forms\Components\Select::make('jenis_pesanan')
                             ->label('Jenis Pesanan')
                             ->options([
                                'Dine-in/Takeaway' => 'Dine-in/Takeaway',
                                'Delivery' => 'Delivery',
                             ])
                             ->disabled() // Make this field read-only
                             ->dehydrated(false),

                        Forms\Components\TextInput::make('total_harga')
                             ->label('Total Harga')
                             ->numeric()
                             ->prefix('Rp')
                             ->disabled() // Make this field read-only
                             ->dehydrated(false),

                        Forms\Components\Select::make('metode_pembayaran')
                            ->label('Metode Pembayaran')
                            ->options([
                                'Tunai' => 'Tunai',
                                'Kartu Debit' => 'Kartu Debit',
                                'Kartu Kredit' => 'Kartu Kredit',
                                'QRIS' => 'QRIS',
                                'Transfer Bank' => 'Transfer Bank',
                            ])
                            ->disabled() // Make this field read-only
                             ->dehydrated(false)
                             ->nullable(),

                         // {{change 2}}
                         // Make Status Pembayaran editable
                         Forms\Components\Select::make('status_pembayaran')
                             ->label('Status Pembayaran')
                             ->options([
                                'Belum Bayar' => 'Belum Bayar',
                                'DP' => 'DP',
                                'Lunas' => 'Lunas',
                                'Dibatalkan' => 'Dibatalkan',
                                'Refund' => 'Refund',
                             ])
                             ->required(), // Keep required as per schema
                         // {{end change 2}}

                         // {{change 3}}
                         // Make Status Pesanan editable
                         Forms\Components\Select::make('status_pesanan')
                              ->label('Status Pesanan')
                              ->options([
                                'Dicatat' => 'Dicatat',
                                'Diproses Koki' => 'Diproses Koki',
                                'Selesai' => 'Selesai',
                                'Dibatalkan' => 'Dibatalkan',
                              ])
                             ->required(), // Keep required as per schema
                         // {{end change 3}}
                    ]),

                // The DetailPesanan Relation Manager will be displayed below this form

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('nomor_struk')
                    ->label('Nomor Struk')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('waktu_pesanan')
                    ->label('Waktu Pesanan')
                    ->dateTime() // Format as date and time
                    ->sortable(),
                 Tables\Columns\TextColumn::make('kasir.nama_lengkap') // Display kasir's name
                    ->label('Kasir')
                    ->searchable()
                    ->sortable(),
                 Tables\Columns\TextColumn::make('pelanggan.nama_pelanggan') // Display customer's name
                    ->label('Pelanggan')
                    ->searchable()
                    ->sortable()
                    ->default('Umum'), // Display 'Umum' if pelanggan is null

                 Tables\Columns\TextColumn::make('jenis_pesanan')
                    ->label('Jenis')
                    ->sortable(),

                 Tables\Columns\TextColumn::make('total_harga')
                    ->label('Total Harga')
                    ->numeric()
                    ->sortable()
                    ->money('IDR'),

                 Tables\Columns\TextColumn::make('status_pembayaran')
                    ->label('Pembayaran')
                    ->badge() // Display as badge
                    ->color(fn (string $state): string => match ($state) { // Add color based on status
                        'Lunas' => 'success',
                        'Belum Bayar' => 'warning',
                        'DP' => 'info',
                        'Dibatalkan', 'Refund' => 'danger',
                        default => 'secondary',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('status_pesanan')
                    ->label('Status Pesanan')
                    ->badge() // Display as badge
                     ->color(fn (string $state): string => match ($state) { // Add color based on status
                        'Selesai' => 'success',
                        'Diproses Koki' => 'info',
                        'Dicatat' => 'warning',
                        'Dibatalkan' => 'danger',
                        default => 'secondary',
                    })
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('id_kasir')
                    ->label('Filter by Kasir')
                    ->relationship('kasir', 'nama_lengkap'),
                Tables\Filters\SelectFilter::make('id_pelanggan')
                     ->label('Filter by Pelanggan')
                     ->relationship('pelanggan', 'nama_pelanggan'),
                Tables\Filters\SelectFilter::make('jenis_pesanan')
                     ->label('Filter by Jenis Pesanan')
                     ->options([
                        'Dine-in/Takeaway' => 'Dine-in/Takeaway',
                        'Delivery' => 'Delivery',
                     ]),
                Tables\Filters\SelectFilter::make('status_pembayaran')
                     ->label('Filter by Status Pembayaran')
                     ->options([
                        'Belum Bayar' => 'Belum Bayar',
                        'DP' => 'DP',
                        'Lunas' => 'Lunas',
                        'Dibatalkan' => 'Dibatalkan',
                        'Refund' => 'Refund',
                     ]),
                Tables\Filters\SelectFilter::make('status_pesanan')
                      ->label('Filter by Status Pesanan')
                      ->options([
                        'Dicatat' => 'Dicatat',
                        'Diproses Koki' => 'Diproses Koki',
                        'Selesai' => 'Selesai',
                        'Dibatalkan' => 'Dibatalkan',
                     ]),
                Tables\Filters\Filter::make('waktu_pesanan')
                     ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('From Date'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Until Date'),
                     ])
                     ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('waktu_pesanan', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('waktu_pesanan', '<=', $date),
                            );
                    })
            ])
            ->actions([
                Tables\Actions\ViewAction::make(), // Add View action
                Tables\Actions\EditAction::make(), // Keep Edit action for status changes
                // Tables\Actions\DeleteAction::make(), // Optional: Remove Delete action if not allowed
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(), // Optional: Remove Bulk Delete action
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // {{change 4}}
            // Register the DetailPesanan Relation Manager
            RelationManagers\DetailPesananRelationManager::class,
            // {{end change 4}}
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\PesananResource\Pages\ListPesanans::route('/'),
            'edit' => \App\Filament\Resources\PesananResource\Pages\EditPesanan::route('/{record}/edit'),
            // {{change 5}}
            // Remove the create page route
            // 'create' => \App\Filament\Resources\PesananResource\Pages\CreatePesanan::route('/create'),
            // {{end change 5}}
        ];
    }

    // {{change 6}}
    // Prevent creation from the list page button
    public static function canCreate(): bool
    {
        return false;
    }
     // {{end change 6}}


    // Optional: Enable global search for this resource
    // public static function isGlobalSearchable(): bool
    // {
    //     return true;
    // }

    // Optional: Define attributes for global search results
    // protected static array $globalSearchResultAttributes = ['nomor_struk', 'kasir.nama_lengkap', 'pelanggan.nama_pelanggan'];

    // Optional: Customize global search result title
    // public static function getGlobalSearchResultTitle(\Illuminate\Database\Eloquent\Model $record): string
    // {
    //     return 'Pesanan #' . $record->nomor_struk;
    // }
}