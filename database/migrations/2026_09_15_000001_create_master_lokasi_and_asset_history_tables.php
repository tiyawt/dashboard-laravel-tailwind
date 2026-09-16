<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_lokasi', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('kode', 20);
            $table->enum('tipe', ['Gedung', 'Lantai', 'Lokasi']);
            $table->timestamps();
            $table->unique(['tipe', 'kode']);
        });

        Schema::create('aset_inventory_sequence_locks', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });

        Schema::create('aset_inventory_sequences', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('tahun');
            $table->foreignId('gedung_id')->constrained('master_lokasi');
            $table->foreignId('lantai_id')->constrained('master_lokasi');
            $table->foreignId('lokasi_id')->constrained('master_lokasi');
            $table->unsignedInteger('last_number')->default(0);
            $table->timestamps();
            $table->unique(['tahun', 'gedung_id', 'lantai_id', 'lokasi_id'], 'aset_inventory_sequence_unique');
        });

        Schema::table('aset_inventaris', function (Blueprint $table) {
            $table->unsignedSmallInteger('tahun_perolehan')->nullable()->after('no_inventaris');
            $table->foreignId('gedung_id')->nullable()->after('tahun_perolehan')->constrained('master_lokasi')->nullOnDelete();
            $table->foreignId('lantai_id')->nullable()->after('gedung_id')->constrained('master_lokasi')->nullOnDelete();
            $table->foreignId('lokasi_id')->nullable()->after('lantai_id')->constrained('master_lokasi')->nullOnDelete();
            $table->enum('status', ['Aktif', 'Nonaktif'])->default('Aktif')->after('keterangan');
            $table->date('tanggal_nonaktif')->nullable()->after('status');
            $table->text('alasan_nonaktif')->nullable()->after('tanggal_nonaktif');
        });

        Schema::create('aset_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aset_inventaris_id')->constrained('aset_inventaris')->cascadeOnDelete();
            $table->string('jenis');
            $table->text('keterangan');
            $table->date('tanggal')->nullable();
            $table->timestamps();
        });

        DB::table('aset_inventory_sequence_locks')->insert([
            'id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('aset_histories');
        Schema::table('aset_inventaris', function (Blueprint $table) {
            $table->dropForeign(['gedung_id']);
            $table->dropForeign(['lantai_id']);
            $table->dropForeign(['lokasi_id']);
            $table->dropColumn(['tahun_perolehan', 'gedung_id', 'lantai_id', 'lokasi_id', 'status', 'tanggal_nonaktif', 'alasan_nonaktif']);
        });
        Schema::dropIfExists('aset_inventory_sequences');
        Schema::dropIfExists('aset_inventory_sequence_locks');
        Schema::dropIfExists('master_lokasi');
    }
};
