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
        Schema::create('minimal_stock', function (Blueprint $table) {
            $table->id();
            // barang_id bersifat unique karena 1 barang hanya punya 1 baris aturan minimal stock
            $table->foreignId('barang_id')
                  ->unique()
                  ->constrained('master_barang')
                  ->cascadeOnDelete();
                  
            $table->integer('minimal')->default(0);
            $table->string('rentang_waktu')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('minimal_stock');
    }
};
