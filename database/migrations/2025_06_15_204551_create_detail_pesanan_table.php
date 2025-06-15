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
        Schema::create('detail_pesanan', function (Blueprint $table) {
            $table->id(); // Corresponds to INT AUTO_INCREMENT PRIMARY KEY
            $table->foreignId('id_pesanan')->constrained('pesanan')->onDelete('cascade'); // Corresponds to FK fk_detail_pesanan with ON DELETE CASCADE
            $table->foreignId('id_menu')->constrained('menu'); // Corresponds to FK fk_detail_menu
            $table->foreignId('id_varian_menu')->nullable()->constrained('varian_menu'); // Corresponds to FK fk_detail_varian
            $table->integer('jumlah');
            $table->decimal('harga_saat_pesan', 12, 2);
            $table->text('catatan_custom')->nullable();
            // Consider adding timestamps if needed
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_pesanan');
    }
};