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
        Schema::create('laporan_sirkulasi', function (Blueprint $table) {
            $table->string('id_laporan')->primary(); // Primary Key log laporan (LAP-...)
            $table->date('rentang_periode_mulai');
            $table->date('rentang_periode_akhir');
            $table->string('jenis_laporan'); // Kategori misalnya 'Bulanan' atau 'Tahunan'
            $table->integer('total_transaksi')->default(0);
            $table->string('id_staf_admin')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('laporan_sirkulasi');
    }
};
