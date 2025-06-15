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
        Schema::create('menu', function (Blueprint $table) {
            $table->id(); // Corresponds to INT AUTO_INCREMENT PRIMARY KEY
            $table->foreignId('id_kategori')->constrained('kategori_menu'); // Corresponds to FK fk_menu_kategori
            $table->string('nama_menu', 150);
            $table->text('deskripsi')->nullable();
            $table->decimal('harga', 12, 2)->nullable()->comment('NULL jika harga ditentukan oleh varian');
            $table->boolean('dapat_dicustom')->default(false);
            // Consider adding timestamps if needed
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu');
    }
};