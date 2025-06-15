<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Alamat extends Model
{
    use HasFactory;

    // Specify the table name
    protected $table = 'alamat';

    // Disable timestamps if your table doesn't have created_at and updated_at columns
    public $timestamps = false;

    // Define the fillable attributes
    protected $fillable = [
        'id_pelanggan',
        'label_alamat',
        'nama_penerima',
        'telepon_penerima',
        'alamat_lengkap',
        'kecamatan',
        'kota',
        'is_default',
    ];

    // The attributes that should be cast.
    protected $casts = [
        'is_default' => 'boolean',
    ];

    /**
     * Get the pelanggan that owns the alamat.
     */
    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan');
    }

    /**
     * Get the pengantaran that uses this alamat.
     */
    public function pengantaran(): HasOne
    {
        return $this->hasOne(Pengantaran::class, 'id_alamat_pengantaran');
    }
}