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
        Schema::create('pengajuan_barang', function (Blueprint $table) {
            $table->id();
            // Foreign Key ke master_barang
            $table->foreignId('barang_id')
                  ->constrained('master_barang')
                  ->cascadeOnDelete();
                  
            $table->date('tanggal_pengajuan');
            $table->integer('volume');
            $table->decimal('harga_per_unit', 15, 2);
            $table->string('link_spb_invoice')->nullable();
            $table->string('permintaan'); // HR/IT/Divisi
            $table->enum('status_barang', ['baru', 'bekas']);
            $table->enum('status_disposisi', ['pending', 'acc', 'rejected'])->default('pending');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_barang');
    }
};
