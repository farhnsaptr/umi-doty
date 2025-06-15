<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pelanggan extends Model
{
    use HasFactory;

    // Specify the table name
    protected $table = 'pelanggan';

    // Disable timestamps if your table doesn't have created_at and updated_at columns
    public $timestamps = false;

    // Define the fillable attributes
    protected $fillable = [
        'nama_pelanggan',
        'nomor_telepon',
    ];

    /**
     * Get the alamat for the pelanggan.
     */
    public function alamat(): HasMany
    {
        return $this->hasMany(Alamat::class, 'id_pelanggan');
    }

    /**
     * Get the pesanan for the pelanggan.
     */
    public function pesanan(): HasMany
    {
        return $this->hasMany(Pesanan::class, 'id_pelanggan');
    }
}