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
        Schema::create('qr_code', function (Blueprint $table) {
            $table->string('id_qrcode')->primary();
            $table->string('id_kendaraan');
            $table->text('data_matriks'); // Lokasi penyimpanan file matriks QR (image path / raw data)
            $table->timestamps();

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
        Schema::dropIfExists('qr_code');
    }
};
