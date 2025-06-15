<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('alamat', function (Blueprint $table) {
            $table->id(); // Corresponds to INT AUTO_INCREMENT PRIMARY KEY
            $table->foreignId('id_pelanggan')->constrained('pelanggan')->onDelete('cascade'); // Corresponds to FK fk_alamat_pelanggan with ON DELETE CASCADE
            $table->string('label_alamat', 50)->comment('Contoh: Rumah, Kantor');
            $table->string('nama_penerima', 100);
            $table->string('telepon_penerima', 20);
            $table->text('alamat_lengkap');
            $table->string('kecamatan', 100)->nullable();
            $table->string('kota', 100);
            $table->boolean('is_default')->default(false);
            // Consider adding timestamps if needed
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alamat');
    }
};