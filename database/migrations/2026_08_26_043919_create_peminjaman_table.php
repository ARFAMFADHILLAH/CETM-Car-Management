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
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->id();
            $table->string('nama_peminjam');
            $table->string('email_peminjam');
            $table->string('no_hp', 20)->nullable();
            $table->foreignId('divisi_id')->nullable()->index()->constrained('divisi')->nullOnDelete();
            $table->foreignId('car_id')->index()->constrained()->restrictOnDelete();
            $table->dateTime('tanggal_mulai')->index();
            $table->dateTime('tanggal_selesai');
            $table->string('kegiatan');
            $table->string('lokasi_tujuan');
            $table->string('nama_customer')->nullable();
            $table->text('catatan')->nullable();
            $table->string('status', 20)->default('pending')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjaman');
    }
};
