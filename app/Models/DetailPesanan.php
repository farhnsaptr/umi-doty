<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailPesanan extends Model
{
    use HasFactory;

    // Specify the table name
    protected $table = 'detail_pesanan';

    // Disable timestamps if your table doesn't have created_at and updated_at columns
    public $timestamps = false;

    // Define the fillable attributes
    protected $fillable = [
        'id_pesanan',
        'id_menu',
        'id_varian_menu',
        'jumlah',
        'harga_saat_pesan',
        'catatan_custom',
    ];

    // The attributes that should be cast.
    protected $casts = [
        'harga_saat_pesan' => 'decimal:2',
    ];

    /**
     * Get the pesanan that owns the detail item.
     */
    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan');
    }

    /**
     * Get the menu item for the detail.
     */
    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'id_menu');
    }

    /**
     * Get the varian menu for the detail.
     */
    public function varianMenu(): BelongsTo
    {
        return $this->belongsTo(VarianMenu::class, 'id_varian_menu');
    }
}