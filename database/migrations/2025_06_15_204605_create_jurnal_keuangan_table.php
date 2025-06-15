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
        Schema::create('jurnal_keuangan', function (Blueprint $table) {
            $table->id(); // Corresponds to INT AUTO_INCREMENT PRIMARY KEY
            $table->date('tanggal');
            $table->string('keterangan', 255);
            $table->foreignId('id_pesanan')->nullable()->constrained('pesanan')->onDelete('set null')->comment('NULL jika ini adalah pengeluaran'); // Corresponds to FK fk_jurnal_pesanan with ON DELETE SET NULL
            $table->decimal('debit', 14, 2)->default(0.00);
            $table->decimal('kredit', 14, 2)->default(0.00);
            // Consider adding timestamps if needed
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jurnal_keuangan');
    }
};