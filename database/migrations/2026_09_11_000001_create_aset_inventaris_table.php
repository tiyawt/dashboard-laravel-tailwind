<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aset_inventaris', function (Blueprint $table) {
            $table->id();
            $table->string('no_inventaris')->unique();
            $table->string('nama_barang');
            $table->text('spesifikasi')->nullable();
            $table->string('lantai')->nullable();
            $table->string('lokasi')->nullable();
            $table->string('nama_user')->nullable();
            $table->text('kelengkapan')->nullable();
            $table->unsignedInteger('jumlah')->default(1);
            $table->date('tanggal_entry');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aset_inventaris');
    }
};
