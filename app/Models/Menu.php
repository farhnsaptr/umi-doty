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

    // {{change 1}}
    // Define the fillable attributes, including the new status_menu
    protected $fillable = [
        'id_kategori',
        'nama_menu',
        'deskripsi',
        'harga',
        'dapat_dicustom',
        'status_menu', // Added the new status_menu column
    ];
    // {{end change 1}}


    // {{change 2}}
    // The attributes that should be cast.
    protected $casts = [
        'harga' => 'decimal:2',
        'dapat_dicustom' => 'boolean',
        'status_menu' => 'string', // Cast status_menu to string for easier handling
        // Optional: You could create a PHP Enum for status_menu and cast to that here
        // 'status_menu' => \App\Enums\MenuStatus::class,
    ];
    // {{end change 2}}


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