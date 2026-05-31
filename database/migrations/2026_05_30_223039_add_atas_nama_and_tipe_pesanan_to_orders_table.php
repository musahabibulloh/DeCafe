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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('atas_nama')->nullable()->after('nama_pelanggan');
            $table->enum('tipe_pesanan', ['dine_in', 'take_away'])->default('dine_in')->after('atas_nama');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['atas_nama', 'tipe_pesanan']);
        });
    }
};
