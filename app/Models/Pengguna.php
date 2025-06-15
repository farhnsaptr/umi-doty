<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pengguna extends Authenticatable
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
        'email', // Make sure 'email' is here
        'password_hash',
    ];

    // The attributes that should be hidden for serialization.
    protected $hidden = [
        'password_hash',
        // 'password', // If you cast 'password_hash' to 'password'
    ];

    // The attributes that should be cast.
    protected $casts = [
        // Add email_verified_at if you implement email verification
        // 'email_verified_at' => 'datetime',
        // 'password_hash' => 'hashed', // Optional: If you want to use Laravel's hashing cast
    ];

    // Override the getAuthPassword method if your password column is not 'password'
    public function getAuthPassword(): string
    {
        return $this->password_hash;
    }

    // {{change 1}}
    /**
     * Get the user's name for display purposes in Filament.
     */
    public function getNameAttribute(): string
    {
        return $this->nama_lengkap; // Or $this->username if you prefer displaying the username
    }
    // {{end change 1}}

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

    // You might need to implement the Authenticatable contract methods
    // if you extend a different base class or customize authentication further.
    // For example, getEmailForPasswordReset, sendPasswordResetNotification, etc.
}