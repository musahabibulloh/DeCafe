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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('kasir_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('kode_pembayaran')->unique();
            $table->enum('metode_pembayaran', ['tunai', 'qris', 'transfer', 'ewallet']);
            $table->integer('total_bayar');
            $table->integer('uang_diterima')->nullable();
            $table->integer('kembalian')->default(0);
            $table->enum('status', ['lunas', 'dibatalkan'])->default('lunas');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
