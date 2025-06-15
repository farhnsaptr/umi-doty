<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VarianMenu extends Model
{
    use HasFactory;

    // Specify the table name
    protected $table = 'varian_menu';

    // Disable timestamps if your table doesn't have created_at and updated_at columns
    public $timestamps = false;

    // Define the fillable attributes
    protected $fillable = [
        'id_menu',
        'nama_varian',
        'harga',
    ];

    // The attributes that should be cast.
    protected $casts = [
        'harga' => 'decimal:2',
    ];

    /**
     * Get the menu that owns the varian.
     */
    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'id_menu');
    }

    /**
     * Get the detail pesanan that use this varian.
     */
    public function detailPesanan(): HasMany
    {
        return $this->hasMany(DetailPesanan::class, 'id_varian_menu');
    }
}