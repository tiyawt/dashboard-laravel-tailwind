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
        Schema::create('penerimaan_barang', function (Blueprint $table) {
            $table->id();
            // Foreign Key ke pengajuan_barang
            $table->foreignId('pengajuan_barang_id')
                  ->constrained('pengajuan_barang')
                  ->cascadeOnDelete();
                  
            $table->date('tanggal_pengambilan')->nullable();
            $table->integer('jumlah_diterima')->default(0);
            $table->string('penerima')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penerimaan_barang');
    }
};
