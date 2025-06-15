<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // Use Authenticatable for users who log in
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pengguna extends Authenticatable // Extend Authenticatable
{
    use HasFactory;

    // Specify the table name
    protected $table = 'pengguna';

    // Disable timestamps if your table doesn't have created_at and updated_at columns
    public $timestamps = false;

    // Define the fillable attributes
    protected $fillable = [
        'id_peran',
        'nama_lengkap',
        'username',
        'password_hash',
    ];

    // The attributes that should be hidden for serialization.
    protected $hidden = [
        'password_hash', // Hide password_hash
    ];

    // The attributes that should be cast.
    protected $casts = [
        // Define any necessary casts, e.g., for password
        // 'password_hash' => 'hashed', // If you want to use Laravel's hashing directly
    ];

    /**
     * Get the peran that owns the pengguna.
     */
    public function peran(): BelongsTo
    {
        return $this->belongsTo(Peran::class, 'id_peran');
    }

    /**
     * Get the pesanan created by the pengguna (as kasir).
     */
    public function pesanan(): HasMany
    {
        return $this->hasMany(Pesanan::class, 'id_kasir');
    }

    /**
     * Get the log aktivitas for the pengguna.
     */
    public function logAktivitas(): HasMany
    {
        return $this->hasMany(LogAktivitas::class, 'id_pengguna');
    }

    // Override the getAuthPassword method if your password column is not 'password'
    public function getAuthPassword(): string
    {
        return $this->password_hash;
    }
}