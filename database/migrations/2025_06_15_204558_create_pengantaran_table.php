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
        Schema::create('pengantaran', function (Blueprint $table) {
            $table->id(); // Corresponds to INT AUTO_INCREMENT PRIMARY KEY
            $table->foreignId('id_pesanan')->unique()->constrained('pesanan')->onDelete('cascade')->comment('Unique untuk relasi 1-ke-1 dengan pesanan'); // Corresponds to FK fk_pengantaran_pesanan with ON DELETE CASCADE and UNIQUE
            $table->foreignId('id_alamat_pengantaran')->constrained('alamat'); // Corresponds to FK fk_pengantaran_alamat
            $table->dateTime('waktu_pengantaran_dijadwalkan');
            $table->dateTime('waktu_pengantaran_aktual')->nullable();
            $table->decimal('biaya_pengantaran', 10, 2)->default(0.00);
            $table->enum('status_pengantaran', ['Menunggu Jadwal', 'Dalam Perjalanan', 'Terkirim', 'Gagal Kirim'])->default('Menunggu Jadwal');
            $table->text('catatan_untuk_kurir')->nullable();
            // Consider adding timestamps if needed
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengantaran');
    }
};