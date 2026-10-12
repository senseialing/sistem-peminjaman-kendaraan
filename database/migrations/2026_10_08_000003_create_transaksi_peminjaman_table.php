<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaksi_peminjaman', function (Blueprint $table) {
            $table->string('id_transaksi')->primary(); // Nomor Tiket Peminjaman (TRX-...)
            $table->string('id_pengguna');
            $table->string('id_kendaraan');
            $table->date('tanggal_pengajuan');
            $table->string('tujuan_operasional');
            $table->enum('status_otorisasi', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->enum('status_sirkulasi', ['Menunggu ACC Pengembalian', 'Tersedia'])->default('Tersedia');
            $table->dateTime('timestamp_serah_terima')->nullable();
            $table->dateTime('timestamp_kembali')->nullable();
            $table->text('catatan_manajer')->nullable();
            $table->timestamps();

            $table->foreign('id_pengguna')
                  ->references('id_pengguna')
                  ->on('users')
                  ->onDelete('cascade');

            $table->foreign('id_kendaraan')
                  ->references('id_kendaraan')
                  ->on('kendaraan')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaksi_peminjaman');
    }
};
