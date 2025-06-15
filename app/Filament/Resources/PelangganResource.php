<?php

namespace App\Filament\Resources;

use App\Models\Pelanggan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PelangganResource extends Resource
{
    protected static ?string $model = Pelanggan::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationLabel = 'Pelanggan';
    protected static ?string $pluralModelLabel = 'Pelanggan';
    protected static ?string $modelLabel = 'Pelanggan';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationGroup = 'Manajemen Data';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_pelanggan')
                    ->label('Nama Pelanggan')
                    ->required()
                    ->maxLength(100),

                Forms\Components\TextInput::make('nomor_telepon')
                    ->label('Nomor Telepon')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(20)
                    ->tel(), // Optional: Hint browser/device for telephone input

                // {{change 1}}
                // Add a Repeater for Alamat (Addresses)
                Forms\Components\Repeater::make('alamat') // Use the relationship name 'alamat'
                    ->label('Alamat Pelanggan')
                    ->relationship('alamat') // Important: Link to the 'alamat' HasMany relationship
                    ->schema([
                        Forms\Components\TextInput::make('label_alamat')
                            ->label('Label Alamat')
                            ->required()
                            ->maxLength(50)
                            ->helperText('Contoh: Rumah, Kantor'),

                        Forms\Components\TextInput::make('nama_penerima')
                            ->label('Nama Penerima')
                            ->required()
                            ->maxLength(100),

                        Forms\Components\TextInput::make('telepon_penerima')
                            ->label('Telepon Penerima')
                            ->required()
                            ->maxLength(20)
                            ->tel(),

                        Forms\Components\Textarea::make('alamat_lengkap')
                            ->label('Alamat Lengkap')
                            ->required()
                            ->columnSpanFull(), // Make textarea take full width

                        Forms\Components\TextInput::make('kecamatan')
                            ->label('Kecamatan')
                            ->maxLength(100)
                            ->nullable(), // Allow null as per schema

                        Forms\Components\TextInput::make('kota')
                            ->label('Kota')
                            ->required()
                            ->maxLength(100),

                        Forms\Components\Toggle::make('is_default')
                            ->label('Atur Sebagai Default')
                            ->inline(false)
                            ->default(false),
                    ])
                    ->columns(2) // Arrange address fields in 2 columns within each item
                    ->defaultItems(1) // Start with one address form by default
                    ->minItems(1) // Require at least one address
                    ->collapsed() // Optional: Start repeater items collapsed
                    ->cloneable() // Allow cloning addresses
                    ->itemLabel(fn (array $state): ?string => filled($state['label_alamat']) ? $state['label_alamat'] : 'Alamat Baru'), // Use label_alamat for item header
                // {{end change 1}}

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('nama_pelanggan')
                    ->label('Nama Pelanggan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nomor_telepon')
                    ->label('Nomor Telepon')
                    ->searchable()
                    ->sortable(),
                // Optional: Count related addresses
                Tables\Columns\TextColumn::make('alamat_count')
                    ->counts('alamat') // Assuming the relationship is named 'alamat' in the Pelanggan model
                    ->label('Jumlah Alamat')
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
        // No Relation Manager needed for Alamat as we use Repeater in the form
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\PelangganResource\Pages\ListPelanggans::route('/'),
            'create' => \App\Filament\Resources\PelangganResource\Pages\CreatePelanggan::route('/create'),
            'edit' => \App\Filament\Resources\PelangganResource\Pages\EditPelanggan::route('/{record}/edit'),
        ];
    }

    // Optional: Enable global search for this resource
    // public static function isGlobalSearchable(): bool
    // {
    //     return true;
    // }

    // Optional: Define attributes for global search results
    // protected static array $globalSearchResultAttributes = ['nama_pelanggan', 'nomor_telepon'];

    // Optional: Customize global search result title
    // public static function getGlobalSearchResultTitle(\Illuminate\Database\Eloquent\Model $record): string
    // {
    //     return $record->nama_pelanggan;
    // }
}