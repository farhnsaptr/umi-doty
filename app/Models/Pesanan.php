<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pesanan extends Model
{
    use HasFactory;

    // Specify the table name
    protected $table = 'pesanan';

    // Disable timestamps if your table doesn't have created_at and updated_at columns
    public $timestamps = false; // Assuming 'waktu_pesanan' is handled manually or with useCurrent() in migration

    // Define the fillable attributes
    protected $fillable = [
        'id_kasir',
        'id_pelanggan',
        'jenis_pesanan',
        'nomor_struk',
        'waktu_pesanan',
        'total_harga',
        'metode_pembayaran',
        'status_pembayaran',
        'status_pesanan',
    ];

    // The attributes that should be cast.
    protected $casts = [
        'waktu_pesanan' => 'datetime',
        'total_harga' => 'decimal:2',
        'jenis_pesanan' => \App\Enums\JenisPesanan::class, // Example: If you create Enums
        'metode_pembayaran' => \App\Enums\MetodePembayaran::class, // Example: If you create Enums
        'status_pembayaran' => \App\Enums\StatusPembayaran::class, // Example: If you create Enums
        'status_pesanan' => \App\Enums\StatusPesanan::class, // Example: If you create Enums
    ];

    /**
     * Get the kasir (pengguna) who created the pesanan.
     */
    public function kasir(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'id_kasir');
    }

    /**
     * Get the pelanggan for the pesanan.
     */
    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan');
    }

    /**
     * Get the detail pesanan for the pesanan.
     */
    public function detailPesanan(): HasMany
    {
        return $this->hasMany(DetailPesanan::class, 'id_pesanan');
    }

    /**
     * Get the pengantaran record associated with the pesanan.
     */
    public function pengantaran(): HasOne
    {
        return $this->hasOne(Pengantaran::class, 'id_pesanan');
    }

    /**
     * Get the jurnal keuangan entries for the pesanan.
     */
    public function jurnalKeuangan(): HasMany
    {
        return $this->hasMany(JurnalKeuangan::class, 'id_pesanan');
    }
}