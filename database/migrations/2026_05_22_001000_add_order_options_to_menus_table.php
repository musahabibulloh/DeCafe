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
        Schema::table('menus', function (Blueprint $table) {
            $table->unsignedTinyInteger('maksimal_lauk')->default(1)->after('gambar');
            $table->boolean('wajib_pilih_lauk')->default(false)->after('maksimal_lauk');
            $table->boolean('wajib_pilih_sambal')->default(false)->after('wajib_pilih_lauk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn([
                'maksimal_lauk',
                'wajib_pilih_lauk',
                'wajib_pilih_sambal',
            ]);
        });
    }
};
