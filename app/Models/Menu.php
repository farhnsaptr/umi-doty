<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany; // Import MorphMany

class Menu extends Model
{
    use HasFactory;

    protected $table = 'menu';
    // Assuming your migration includes created_at and updated_at
    public $timestamps = false;

    protected $fillable = [
        'id_kategori',
        'nama_menu',
        'deskripsi',
        'harga',
        'dapat_dicustom',
        'status_menu',
        // 'slug', // Uncomment if added
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'dapat_dicustom' => 'boolean',
        'status_menu' => 'string', // Or your Enum class
        // 'created_at' => 'datetime', // Uncomment if added
        // 'updated_at' => 'datetime', // Uncomment if added
    ];

    /**
     * Get the kategori that owns the menu.
     */
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriMenu::class, 'id_kategori');
    }

    /**
     * Get the varian for the menu.
     */
    public function varian(): HasMany
    {
        return $this->hasMany(VarianMenu::class, 'id_menu');
    }

    /**
     * Get the detail pesanan for the menu.
     */
    public function detailPesanan(): HasMany
    {
        return $this->hasMany(DetailPesanan::class, 'id_menu');
    }

    /**
     * Get the files associated with the menu (e.g., photos).
     * MorphMany relationship: This Menu model is the parent ('fileable')
     * of multiple File models.
     * The method name 'files' MUST match FileUpload::make('files') in the Resource.
     * The morph name 'fileable' MUST match the morphs() in the migration and morphTo() in File model.
     */
    public function files(): MorphMany
    {
        return $this->morphMany(File::class, 'fileable'); // Ensure 'fileable' is correct morph name
    }
}