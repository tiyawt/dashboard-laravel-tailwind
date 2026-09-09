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
        Schema::create('stok_keluar_lemari', function (Blueprint $table) {
            $table->id();
            // Foreign Key ke master_barang
            $table->foreignId('barang_id')
                ->constrained('master_barang')
                ->cascadeOnDelete();

            $table->string('kondisi_barang_lama')->nullable();
            $table->date('tanggal_keluar');
            $table->integer('jumlah');
            $table->string('pelapor');
            $table->string('lokasi');
            $table->enum('status', ['done', 'not_yet'])->default('not_yet');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok_keluar_lemari');
    }
};
