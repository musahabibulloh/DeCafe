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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pesanan')->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('nama_pelanggan')->nullable();
            $table->string('nomor_meja');
            $table->integer('total_harga')->default(0);
            $table->enum('status_pesanan', [
                'menunggu',
                'diterima_dapur',
                'diproses',
                'siap_saji',
                'selesai',
                'dibatalkan',
            ])->default('menunggu');
            $table->enum('status_pembayaran', [
                'belum_bayar',
                'lunas',
                'dibatalkan',
            ])->default('belum_bayar');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
