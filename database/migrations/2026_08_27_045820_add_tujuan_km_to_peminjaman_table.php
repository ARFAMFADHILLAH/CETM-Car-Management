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
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->renameColumn('kegiatan', 'keperluan');
            $table->string('tujuan')->after('lokasi_tujuan');
            $table->unsignedBigInteger('km_awal')->after('tujuan');
            $table->unsignedBigInteger('km_akhir')->nullable()->after('km_awal');
            $table->string('tangki_bbm')->after('km_akhir');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->dropColumn(['tujuan', 'km_awal', 'km_akhir', 'tangki_bbm']);
            $table->renameColumn('keperluan', 'kegiatan');
        });
    }
};
