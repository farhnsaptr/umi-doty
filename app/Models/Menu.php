<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    use HasFactory;

    // Specify the table name
    protected $table = 'menu';

    // Disable timestamps if your table doesn't have created_at and updated_at columns
    public $timestamps = false;

    // Define the fillable attributes
    protected $fillable = [
        'id_kategori',
        'nama_menu',
        'deskripsi',
        'harga',
        'dapat_dicustom',
    ];

    // The attributes that should be cast.
    protected $casts = [
        'harga' => 'decimal:2',
        'dapat_dicustom' => 'boolean',
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
}