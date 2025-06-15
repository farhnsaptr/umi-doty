<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pengantaran extends Model
{
    use HasFactory;

    // Specify the table name
    protected $table = 'pengantaran';

    // Disable timestamps if your table doesn't have created_at and updated_at columns
    public $timestamps = false; // Assuming datetime columns are handled manually

    // Define the fillable attributes
    protected $fillable = [
        'id_pesanan',
        'id_alamat_pengantaran',
        'waktu_pengantaran_dijadwalkan',
        'waktu_pengantaran_aktual',
        'biaya_pengantaran',
        'status_pengantaran',
        'catatan_untuk_kurir',
    ];

    // The attributes that should be cast.
    protected $casts = [
        'waktu_pengantaran_dijadwalkan' => 'datetime',
        'waktu_pengantaran_aktual' => 'datetime',
        'biaya_pengantaran' => 'decimal:2',
        'status_pengantaran' => \App\Enums\StatusPengantaran::class, // Example: If you create Enums
    ];

    /**
     * Get the pesanan associated with the pengantaran.
     */
    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan');
    }

    /**
     * Get the alamat pengantaran.
     */
    public function alamatPengantaran(): BelongsTo
    {
        return $this->belongsTo(Alamat::class, 'id_alamat_pengantaran');
    }
}