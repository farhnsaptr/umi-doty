<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Menu extends Model
{
    use HasFactory;

    protected $table = 'menu';
    public $timestamps = true;

    protected $fillable = [
        'id_kategori',
        'nama_menu',
        'deskripsi',
        'harga',
        'dapat_dicustom',
        'status_menu',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'dapat_dicustom' => 'boolean',
        'status_menu' => 'string',
    ];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriMenu::class, 'id_kategori');
    }

    public function varian(): HasMany
    {
        return $this->hasMany(VarianMenu::class, 'id_menu');
    }

    public function detailPesanan(): HasMany
    {
        return $this->hasMany(DetailPesanan::class, 'id_menu');
    }

    public function files(): MorphMany
    {
        return $this->morphMany(File::class, 'fileable');
    }
}