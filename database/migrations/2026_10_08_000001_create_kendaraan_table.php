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
        Schema::create('kendaraan', function (Blueprint $table) {
            $table->string('id_kendaraan')->primary(); // Kode unit/nomor inventaris kendaraan
            $table->string('nama_kendaraan');
            $table->string('plat_nomor');
            $table->text('foto_kendaraan')->nullable();
            $table->integer('kapasitas');
            $table->enum('status_ketersediaan', ['Tersedia', 'Sedang Dipinjam'])->default('Tersedia');
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
        Schema::dropIfExists('kendaraan');
    }
};
