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
            $table->string('bukti_pembayaran')->nullable()->after('status_pembayaran');
            $table->string('status_pembayaran')->default('belum_bayar')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('bukti_pembayaran');
            // Note: DB drivers don't easily change back string to enum natively without specifying enum values,
            // but keeping it string is safe and compatible.
        });
    }
};
