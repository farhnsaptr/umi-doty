<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JurnalKeuangan extends Model
{
    use HasFactory;

    // Specify the table name
    protected $table = 'jurnal_keuangan';

    // Disable timestamps if your table doesn't have created_at and updated_at columns
    public $timestamps = false;

    // Define the fillable attributes
    protected $fillable = [
        'tanggal',
        'keterangan',
        'id_pesanan',
        'debit',
        'kredit',
    ];

    // The attributes that should be cast.
    protected $casts = [
        'tanggal' => 'date',
        'debit' => 'decimal:2',
        'kredit' => 'decimal:2',
    ];

    /**
     * Get the pesanan associated with the jurnal entry.
     */
    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan');
    }
}