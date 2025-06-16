<?php

namespace App\Filament\Resources;

use App\Models\Menu;
use App\Models\KategoriMenu;
use App\Models\File; // Import the File model explicitly
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;


class MenuResource extends Resource
{
    protected static ?string $model = Menu::class;
    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationLabel = 'Menu';
    protected static ?string $pluralModelLabel = 'Menu';
    protected static ?string $modelLabel = 'Menu';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationGroup = 'Manajemen Menu';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(2)
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
                            ->maxLength(65535)
                            ->columnSpanFull(),

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
                            ->itemLabel(fn (array $state): ?string => filled($state['nama_varian']) ? $state['nama_varian'] : 'Nama Varian Belum Diisi')
                            ->columnSpanFull(),
                    ]),

                 // FileUpload component for adding photos to the Menu item
                 // The 'files' name MUST match the polymorphic relationship method in the Menu model (files())
                 Forms\Components\FileUpload::make('files') // THIS NAME IS CRUCIAL
                     ->label('Foto Menu')
                     ->multiple() // Allow multiple files
                     ->image() // Only allow image files
                     ->disk('public') // Storage disk
                     ->directory('menu-photos') // Directory within the disk
                     ->reorderable() // Allow reordering
                     ->appendFiles() // Keep existing files
                     // Corrected deleteUploadedFileUsing closure with explicit type handling and logging
                     // This closure is complex due to handling both new unsaved files (string path)
                     // and existing saved files (array representation or model instance).
                     ->deleteUploadedFileUsing(function (\Filament\Forms\Components\FileUpload $component, $file): void {
                           Log::info('FileUpload deleteUploadedFileUsing called', [
                               'file_type' => gettype($file),
                               'file_content_sample' => (is_array($file) || is_object($file)) ? json_encode($file, JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_PRETTY_PRINT) : $file, // Log content, handle arrays/objects
                           ]);

                           // Determine the file path. The structure of $file varies.
                           $filePath = null;
                            if (is_string($file)) {
                                // This is a newly uploaded file path before saving to DB
                                $filePath = $file;
                                Log::info('Delete: Detected string path for new file.');
                            } elseif (is_array($file) && isset($file['path'])) {
                                // This is often an array representation of an existing File model
                                $filePath = $file['path'];
                                Log::info('Delete: Detected array with path for existing file.');
                            } elseif ($file instanceof \Illuminate\Database\Eloquent\Model && isset($file->path)) {
                                // This might be the actual File model instance
                                $filePath = $file->path;
                                Log::info('Delete: Detected File model instance.');
                            } else {
                                Log::warning('Delete: Could not determine file path from provided data.', ['provided_data_type' => gettype($file)]);
                            }

                           Log::info('Delete: Determined file path:', ['filePath' => $filePath]);

                           if ($filePath) {
                                // Try to find the corresponding File model record by its path
                                $fileModel = File::where('path', $filePath)->first();

                                if ($fileModel) {
                                    // If the model record exists, delete it.
                                    // The File model's booted method should handle deleting the physical file.
                                    Log::info('Delete: Found File model, calling delete().', ['id' => $fileModel->id, 'path' => $fileModel->path]);
                                    $fileModel->delete(); // This should trigger the File model's deleting event
                                } else {
                                    // If no model record exists (e.g., a new upload deleted before saving
                                    // or an issue prevented model creation previously),
                                    // just delete the physical file directly from storage.
                                    Log::warning('Delete: File model not found, attempting to delete physical file directly:', ['filePath' => $filePath]);
                                    if (Storage::disk($component->getDiskName())->exists($filePath)) {
                                         $deleted = Storage::disk($component->getDiskName())->delete($filePath);
                                         Log::info('Delete: Physical file deleted directly:', ['filePath' => $filePath, 'success' => $deleted]);
                                    } else {
                                         Log::warning('Delete: Physical file not found for direct deletion:', ['filePath' => $filePath]);
                                    }
                                }
                           } else {
                                Log::error('Delete: File path is null, cannot proceed with deletion.');
                           }
                     })
                     ->columnSpanFull(), // Take full width


            ]); // End form schema
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

                Tables\Columns\TextColumn::make('files_count')
                    ->counts('files')
                    ->label('Foto')
                    ->sortable(),

                 // {{change 2}}
                 // Display the first image from the files relationship
                 // This requires eager loading the 'files' relationship in ListMenus.php
                 // Access the 'path' attribute of the first item (index 0) in the 'files' collection.
                 // Ensure the relationship is correctly set up and fillable in the File model.
                 Tables\Columns\ImageColumn::make('files.0.path')
                     ->label('Foto')
                     ->square()
                     ->disk('public'), // Specify the storage disk
                 // {{end change 2}}

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