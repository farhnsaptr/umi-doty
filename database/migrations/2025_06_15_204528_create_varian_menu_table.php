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
        Schema::create('varian_menu', function (Blueprint $table) {
            $table->id(); // Corresponds to INT AUTO_INCREMENT PRIMARY KEY
            $table->foreignId('id_menu')->constrained('menu')->onDelete('cascade'); // Corresponds to FK fk_varian_menu with ON DELETE CASCADE
            $table->string('nama_varian', 50);
            $table->decimal('harga', 12, 2);
            // Consider adding timestamps if needed
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('varian_menu');
    }
};