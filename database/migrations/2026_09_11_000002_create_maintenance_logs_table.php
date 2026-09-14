<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aset_inventaris_id')->constrained('aset_inventaris')->cascadeOnDelete();
            $table->date('tanggal');
            $table->string('pelapor');
            $table->text('gejala_masalah');
            $table->text('penyebab')->nullable();
            $table->text('tindakan_penanganan')->nullable();
            $table->string('teknisi')->nullable();
            $table->string('status')->default('Open');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_logs');
    }
};
