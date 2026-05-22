<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Expand enum options to include 'admin', 'kasir', 'pelayan', 'dapur', and 'customer'
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'kasir', 'pelayan', 'dapur', 'customer'])->default('customer')->change();
        });
    }

    public function down(): void
    {
        // Revert role of 'customer' users to 'pelayan' before narrowing the enum
        DB::table('users')
            ->where('role', 'customer')
            ->update(['role' => 'pelayan']);

        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'kasir', 'pelayan', 'dapur'])->default('pelayan')->change();
        });
    }
};