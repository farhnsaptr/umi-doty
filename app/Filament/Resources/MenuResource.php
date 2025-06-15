<?php

namespace App\Filament\Resources;

use App\Models\Menu;
use App\Models\KategoriMenu;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;


class MenuResource extends Resource
{
    protected static ?string $model = Menu::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationLabel = 'Menu';
    protected static ?string $pluralModelLabel = 'Menu';
    protected static ?string $modelLabel = 'Menu';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('id_kategori')
                    ->label('Kategori')
                    ->relationship('kategori', 'nama_kategori')
                    ->required()
                    ->native(false)
                    ->searchable()
                    ->preload(),

                Forms\Components\TextInput::make('nama_menu')
                    ->label('Nama Menu')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(150),

                Forms\Components\Select::make('status_menu')
                    ->label('Status Menu')
                    ->options([
                        'Tersedia' => 'Tersedia',
                        'habis' => 'Habis',
                        'tersembunyi' => 'Tersembunyi',
                    ])
                    ->required()
                    ->default('Tersedia')
                    ->native(false),

                Forms\Components\Textarea::make('deskripsi')
                    ->label('Deskripsi')
                    ->maxLength(65535),

                Forms\Components\TextInput::make('harga')
                    ->label('Harga')
                    ->numeric()
                    ->prefix('Rp')
                    ->nullable()
                    ->helperText('Biarkan kosong jika harga ditentukan oleh varian.')
                    ->hidden(fn (Forms\Get $get): bool => (bool) $get('dapat_dicustom'))
                    ->required(fn (Forms\Get $get): bool => !(bool) $get('dapat_dicustom')),


                Forms\Components\Toggle::make('dapat_dicustom')
                    ->label('Terdapat Varian')
                    ->inline(false)
                    ->default(false)
                    ->live()
                    ->columnSpanFull(),

                // --- Repeater for Varian Menu ---
                Forms\Components\Repeater::make('varian')
                    ->label('Varian Menu')
                    ->relationship('varian')
                    ->schema([
                        Forms\Components\TextInput::make('nama_varian')
                            ->label('Nama Varian')
                            ->required()
                            ->maxLength(50),
                        Forms\Components\TextInput::make('harga')
                            ->label('Harga Varian')
                            ->required()
                            ->numeric()
                            ->prefix('Rp'),
                    ])
                    ->columns(2)
                    ->minItems(1)
                    ->collapsed()
                    ->visible(fn (Forms\Get $get): bool => (bool) $get('dapat_dicustom'))
                    ->cloneable()
                    // {{change 1}}
                    // Use itemLabel() to set the header text for each repeater item
                    ->itemLabel(fn (array $state): ?string => filled($state['nama_varian']) ? $state['nama_varian'] : 'Nama Varian Belum Diisi'),
                    // {{end change 1}}
                // --- End Repeater ---

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('kategori.nama_kategori')
                    ->label('Kategori')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('nama_menu')
                    ->label('Nama Menu')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status_menu')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Tersedia' => 'success',
                        'habis' => 'danger',
                        'tersembunyi' => 'warning',
                        default => 'secondary',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('harga')
                    ->label('Harga')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->money('IDR')
                    ->state(fn (Menu $record): ?string => $record->dapat_dicustom ? 'Lihat Varian' : number_format($record->harga, 2, ',', '.')),


                Tables\Columns\IconColumn::make('dapat_dicustom')
                    ->label('Terdapat Varian')
                    ->boolean()
                    ->toggleable(),

            ])
            ->filters([
                Tables\Filters\SelectFilter::make('id_kategori')
                    ->label('Filter by Kategori')
                    ->relationship('kategori', 'nama_kategori')
                    ->native(false),
                 Tables\Filters\TernaryFilter::make('dapat_dicustom')
                    ->label('Terdapat Varian')
                    ->trueLabel('Ya')
                    ->falseLabel('Tidak')
                    ->placeholder('Semua'),
                Tables\Filters\SelectFilter::make('status_menu')
                    ->label('Filter by Status')
                     ->options([
                        'Tersedia' => 'Tersedia',
                        'habis' => 'Habis',
                        'tersembunyi' => 'Tersembunyi',
                    ])
                    ->native(false),
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
            'index' => \App\Filament\Resources\MenuResource\Pages\ListMenus::route('/'),
            'create' => \App\Filament\Resources\MenuResource\Pages\CreateMenu::route('/create'),
            'edit' => \App\Filament\Resources\MenuResource\Pages\EditMenu::route('/{record}/edit'),
        ];
    }
}