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
        $cols = array_column(DB::select("SHOW COLUMNS FROM `surat`"), 'Field');

        if (!in_array('no_agenda', $cols)) {
            DB::statement("ALTER TABLE `surat` ADD `no_agenda` INT UNSIGNED NULL AFTER `id`");
        }

        // Backfill no_agenda untuk surat masuk yang sudah ada
        $suratMasukList = DB::table('surat')
            ->where('jenis_surat', 'Masuk')
            ->whereNull('no_agenda')
            ->orderBy('id', 'asc')
            ->get();

        $agendaCounter = 1;
        foreach ($suratMasukList as $s) {
            DB::table('surat')->where('id', $s->id)->update([
                'no_agenda' => $agendaCounter++,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $cols = array_column(DB::select("SHOW COLUMNS FROM `surat`"), 'Field');

        if (in_array('no_agenda', $cols)) {
            DB::statement("ALTER TABLE `surat` DROP COLUMN `no_agenda`");
        }
    }
};

