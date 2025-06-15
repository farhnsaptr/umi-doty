<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriMenu extends Model
{
    use HasFactory;

    // Specify the table name
    protected $table = 'kategori_menu';

    // Disable timestamps if your table doesn't have created_at and updated_at columns
    public $timestamps = false;

    // Define the fillable attributes
    protected $fillable = [
        'nama_kategori',
    ];

    /**
     * Get the menu items for the category.
     */
    public function menu(): HasMany
    {
        return $this->hasMany(Menu::class, 'id_kategori');
    }
}