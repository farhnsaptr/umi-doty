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
    protected static ?int $navigationSort = 2; // Order within 'Manajemen Menu' group
    protected static ?string $navigationGroup = 'Manajemen Menu';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\Select::make('id_kategori')
                            ->label('Kategori')
                            ->relationship('kategori', 'nama_kategori') // Link to KategoriMenu relationship
                            ->required()
                            ->native(false) // Better styling for select
                            ->searchable() // Enable searching within the select dropdown
                            ->preload(), // Load all options upfront

                        Forms\Components\TextInput::make('nama_menu')
                            ->label('Nama Menu')
                            ->required()
                            ->unique(ignoreRecord: true) // Ensure menu names are unique (ignore current record on edit)
                            ->maxLength(150),

                         Forms\Components\Select::make('status_menu')
                            ->label('Status Menu')
                            // Define the options for the ENUM field
                            ->options([
                                'Tersedia' => 'Tersedia',
                                'habis' => 'Habis',
                                'tersembunyi' => 'Tersembunyi',
                            ])
                            ->required()
                            ->default('Tersedia') // Set the default value
                            ->native(false), // Better styling

                        Forms\Components\Textarea::make('deskripsi')
                            ->label('Deskripsi')
                            ->maxLength(65535) // TEXT database type limit
                            ->columnSpanFull(), // Make description take full width in the grid

                        Forms\Components\TextInput::make('harga')
                            ->label('Harga')
                            ->numeric() // Restrict input to numbers
                            ->prefix('Rp') // Add 'Rp' before the input
                            ->nullable() // Allow price to be null if variants exist
                            ->helperText('Biarkan kosong jika harga ditentukan oleh varian.') // Explanatory text below field
                            // Hide this field if 'dapat_dicustom' toggle is true
                            ->hidden(fn (Forms\Get $get): bool => (bool) $get('dapat_dicustom'))
                            // Require this field only if 'dapat_dicustom' toggle is false
                            ->required(fn (Forms\Get $get): bool => !(bool) $get('dapat_dicustom')),

                        Forms\Components\Toggle::make('dapat_dicustom')
                            ->label('Terdapat Varian') // Label for the toggle
                            ->inline(false) // Place label above toggle
                            ->default(false) // Set default state
                            ->live() // Make the toggle state reactive (updates other fields/components)
                            ->columnSpanFull(), // Make toggle take full width

                        Forms\Components\Repeater::make('varian')
                            ->label('Varian Menu')
                            ->relationship('varian') // Link Repeater to the 'varian' HasMany relationship
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
                            ->columns(2) // Arrange variant fields in 2 columns within each item
                            ->minItems(1) // Require at least one variant if the section is visible
                            ->collapsed() // Start repeater items collapsed
                            // Only show this Repeater if 'dapat_dicustom' toggle is true
                            ->visible(fn (Forms\Get $get): bool => (bool) $get('dapat_dicustom'))
                            ->cloneable() // Allow users to duplicate existing variant items
                            // Customize the header text for each repeater item
                            ->itemLabel(fn (array $state): ?string => filled($state['nama_varian']) ? $state['nama_varian'] : 'Nama Varian Belum Diisi')
                            ->columnSpanFull(), // Make the entire repeater take full width
                    ]), // End Grid schema


                 // FileUpload component for adding photos to the Menu item
                 // The 'files' name MUST match the polymorphic relationship method in the Menu model (files())
                 Forms\Components\FileUpload::make('files') // THIS NAME IS CRUCIAL
                     ->label('Foto Menu')
                     ->multiple() // Allow multiple files to be uploaded
                     ->image() // Restrict uploads to image file types
                     ->disk('public') // Specify the storage disk (configured in config/filesystems.php)
                     ->directory('menu-photos') // Specify subdirectory within the disk for these files
                     ->reorderable() // Allow changing the order of uploaded files
                     ->appendFiles() // Keep existing files when new ones are uploaded
                     // {{change 1}}
                     // ******************************************************************
                     // CORRECTED: Removed 'string' type hint from the $file argument.
                     // This is the crucial fix for the "Argument #1 must be of type string, array given" error.
                     // The closure must accept mixed types as Filament passes different data structures here.
                     // ******************************************************************
                     ->deleteUploadedFileUsing(function (\Filament\Forms\Components\FileUpload $component, $file): void { // <-- REMOVED 'string' TYPE HINT HERE
                           // Log the type and content of the $file variable being passed for debugging
                           Log::info('FileUpload deleteUploadedFileUsing called', [
                               'file_type' => gettype($file),
                               'file_content_sample' => (is_array($file) || is_object($file)) ? json_encode($file, JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_PRETTY_PRINT) : $file,
                           ]);

                           // Determine the file path based on the type of $file data passed
                           $filePath = null;
                            if (is_string($file)) {
                                // If $file is a string, it's likely a path for a newly uploaded file
                                $filePath = $file;
                                Log::info('Delete: Detected string path for new file.');
                            } elseif (is_array($file) && isset($file['path'])) {
                                // If $file is an array, check for the 'path' key (common for existing files)
                                $filePath = $file['path'];
                                Log::info('Delete: Detected array with path for existing file.');
                            } elseif ($file instanceof \Illuminate\Database\Eloquent\Model && isset($file->path)) {
                                 // If $file is a model instance, get the path from the model
                                 $filePath = $file->path;
                                 Log::info('Delete: Detected File model instance.');
                            } else {
                                Log::warning('Delete: Could not determine file path from provided data.', ['provided_data_type' => gettype($file)]);
                            }

                           Log::info('Delete: Determined file path:', ['filePath' => $filePath]);

                           if ($filePath) {
                               // Try to find the corresponding File model record by its path
                               // Use the File model imported at the top
                               $fileModel = File::where('path', $filePath)->first();

                                if ($fileModel) {
                                    // If the model record exists, delete it.
                                    // The File model's booted method should handle deleting the physical file.
                                    Log::info('Delete: Found File model, calling delete().', ['id' => $fileModel->id, 'path' => $fileModel->path]);
                                    $fileModel->delete(); // This should trigger the File model's deleting event
                                } else {
                                    // If no model record exists (e.g., a new upload deleted before saving),
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
                     // {{end change 1}}
                     ->columnSpanFull(),


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

                Tables\Columns\TextColumn::make('files_count')
                    ->counts('files')
                    ->label('Foto')
                    ->sortable(),

                 Tables\Columns\ImageColumn::make('files.0.path') // Access the 'path' of the first item (index 0) in the 'files' collection
                     ->label('Foto')
                     ->square()
                     ->disk('public'),
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