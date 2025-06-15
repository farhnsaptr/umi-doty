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
        Schema::create('pesanan', function (Blueprint $table) {
            $table->id(); // Corresponds to INT AUTO_INCREMENT PRIMARY KEY
            $table->foreignId('id_kasir')->constrained('pengguna'); // Corresponds to FK fk_pesanan_kasir
            $table->foreignId('id_pelanggan')->nullable()->constrained('pelanggan'); // Corresponds to FK fk_pesanan_pelanggan
            $table->enum('jenis_pesanan', ['Dine-in/Takeaway', 'Delivery'])->default('Dine-in/Takeaway');
            $table->string('nomor_struk', 25)->unique();
            $table->timestamp('waktu_pesanan')->useCurrent(); // Corresponds to TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
            $table->decimal('total_harga', 14, 2);
            $table->enum('metode_pembayaran', ['Tunai', 'Kartu Debit', 'Kartu Kredit', 'QRIS', 'Transfer Bank'])->nullable();
            $table->enum('status_pembayaran', ['Belum Bayar', 'DP', 'Lunas', 'Dibatalkan', 'Refund'])->default('Belum Bayar');
            $table->enum('status_pesanan', ['Dicatat', 'Diproses Koki', 'Selesai', 'Dibatalkan'])->default('Dicatat');
            // Consider adding timestamps if needed
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesanan');
    }
};