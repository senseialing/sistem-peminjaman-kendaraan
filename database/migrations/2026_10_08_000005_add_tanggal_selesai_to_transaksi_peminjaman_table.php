<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('transaksi_peminjaman', function (Blueprint $table) {
            $table->date('tanggal_selesai')->nullable()->after('tanggal_pengajuan');
        });
    }

    public function down()
    {
        Schema::table('transaksi_peminjaman', function (Blueprint $table) {
            $table->dropColumn('tanggal_selesai');
        });
    }
};
