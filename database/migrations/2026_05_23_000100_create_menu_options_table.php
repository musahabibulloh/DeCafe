<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('menu_options')) {
            Schema::create('menu_options', function (Blueprint $table) {
                $table->id();
                $table->foreignId('menu_id')->constrained()->cascadeOnDelete();
                $table->string('nama_opsi');
                $table->enum('tipe', ['lauk', 'sambal', 'ekstra_lauk'])->default('lauk');
                $table->enum('status', ['tersedia', 'habis'])->default('tersedia');
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }

        DB::table('menus')
            ->where('nama_menu', 'like', '%Nasi bakar%')
            ->update([
                'wajib_pilih_lauk' => true,
                'wajib_pilih_sambal' => true,
            ]);

        DB::table('menus')
            ->where('nama_menu', 'like', '%mix%')
            ->update(['maksimal_lauk' => 2]);

        $this->seedExistingNasiBakarOptions();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_options');
    }

    private function seedExistingNasiBakarOptions(): void
    {
        $menus = DB::table('menus')
            ->where('nama_menu', 'like', '%Nasi bakar%')
            ->get();

        foreach ($menus as $menu) {
            $exists = DB::table('menu_options')->where('menu_id', $menu->id)->exists();

            if ($exists) {
                continue;
            }

            $rows = [];
            $sortOrder = 0;

            foreach ($this->optionsFor($menu, 'opsi_lauk', $this->defaultLauk()) as $option) {
                $rows[] = $this->optionRow($menu->id, $option, 'lauk', $sortOrder++);
            }

            foreach ($this->optionsFor($menu, 'opsi_sambal', $this->defaultSambal()) as $option) {
                $rows[] = $this->optionRow($menu->id, $option, 'sambal', $sortOrder++);
            }

            foreach ($this->optionsFor($menu, 'opsi_ekstra_lauk', $this->defaultEkstra()) as $option) {
                $rows[] = $this->optionRow($menu->id, $option, 'ekstra_lauk', $sortOrder++);
            }

            if (!empty($rows)) {
                DB::table('menu_options')->insert($rows);
            }
        }
    }

    private function optionsFor(object $menu, string $legacyColumn, array $fallback): array
    {
        if (Schema::hasColumn('menus', $legacyColumn) && !empty($menu->{$legacyColumn})) {
            $decoded = json_decode($menu->{$legacyColumn}, true);

            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return $fallback;
    }

    private function optionRow(int $menuId, string $option, string $type, int $sortOrder): array
    {
        return [
            'menu_id' => $menuId,
            'nama_opsi' => $option,
            'tipe' => $type,
            'status' => 'tersedia',
            'sort_order' => $sortOrder,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    private function defaultLauk(): array
    {
        return [
            'Ayam suwir',
            'Ikan tuna',
            'Usus ayam',
            'Ati ayam',
            'Ampela ayam',
            'Telur dadar',
            'Kulit sapi/ kikil / cecek',
            'Cumi *',
            'Tetelam *',
            'Paru *',
        ];
    }

    private function defaultSambal(): array
    {
        return [
            'Sambal bawang',
            'Sambal ijo',
            'Sambal pedas manis',
            'Sambal matah',
            'Sambal nanas*',
            'Sambal bajak*',
            'Tanpa sambal',
        ];
    }

    private function defaultEkstra(): array
    {
        return [
            'Jamur krispi*',
            'Telur asin*',
            'Sate kulit*',
        ];
    }
};
