<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogAktivitas extends Model
{
    use HasFactory;

    // Specify the table name
    protected $table = 'log_aktivitas';

    // Disable timestamps if your table doesn't have created_at and updated_at columns
    public $timestamps = false; // Assuming 'waktu_aktivitas' is handled with useCurrent()

    // Define the fillable attributes
    protected $fillable = [
        'id_pengguna',
        'username',
        'waktu_aktivitas',
        'jenis_aktivitas',
        'deskripsi',
    ];

    // The attributes that should be cast.
    protected $casts = [
        'waktu_aktivitas' => 'datetime',
    ];

    /**
     * Get the pengguna who performed the activity.
     */
    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna');
    }
}