<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable; // Added if you plan to use notifications

class Pengguna extends Authenticatable
{
    use HasFactory, Notifiable; // Added Notifiable

    // Specify the table name if it doesn't match Laravel's pluralization
    protected $table = 'pengguna';

    // Disable timestamps if your table doesn't have created_at and updated_at columns
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_peran',
        'nama_lengkap',
        'username',
        'email', // Ensure 'email' is here
        'password_hash', // ENSURE password_hash IS FILLABLE
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password_hash', // Hide password_hash from serialization
        // 'password', // If you cast 'password_hash' to 'password'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        // 'email_verified_at' => 'datetime', // Uncomment if you add email verification
        // 'password_hash' => 'hashed', // Optional: Use Laravel's hashing cast if preferred (alternative to dehydrateStateUsing)
    ];

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword(): string
    {
        return $this->password_hash;
    }

    /**
     * Get the user's name for display purposes in Filament.
     */
    public function getNameAttribute(): string
    {
        return $this->nama_lengkap; // Or $this->username
    }

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

    // If you implement password reset, you might need this:
    // public function sendPasswordResetNotification($token): void
    // {
    //     $this->notify(new \App\Notifications\ResetPasswordNotification($token)); // Example notification
    // }
}