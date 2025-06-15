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
        Schema::create('log_aktivitas', function (Blueprint $table) {
            $table->bigIncrements('id'); // Corresponds to BIGINT AUTO_INCREMENT PRIMARY KEY
            $table->foreignId('id_pengguna')->nullable()->constrained('pengguna')->onDelete('set null')->comment('NULL jika aksi dilakukan oleh sistem'); // Corresponds to FK fk_log_pengguna with ON DELETE SET NULL
            $table->string('username', 50)->comment('Salinan username untuk arsip');
            $table->timestamp('waktu_aktivitas')->useCurrent(); // Corresponds to TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
            $table->string('jenis_aktivitas', 50);
            $table->text('deskripsi');
            // Consider adding timestamps if needed
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_aktivitas');
    }
};