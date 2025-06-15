<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Peran extends Model
{
    use HasFactory;

    // Specify the table name if it doesn't match the model name pluralized
    protected $table = 'peran';

    // Disable timestamps if your table doesn't have created_at and updated_at columns
    public $timestamps = false;

    // Define the fillable attributes for mass assignment
    protected $fillable = [
        'nama_peran',
    ];

    /**
     * Get the pengguna for the peran.
     */
    public function pengguna(): HasMany
    {
        return $this->hasMany(Pengguna::class, 'id_peran');
    }
}