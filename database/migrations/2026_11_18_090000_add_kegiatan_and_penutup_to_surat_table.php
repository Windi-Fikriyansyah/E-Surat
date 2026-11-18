<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $cols = array_column(DB::select("SHOW COLUMNS FROM `surat`"), 'Field');

        if (!in_array('isi_surat', $cols)) {
            DB::statement("ALTER TABLE `surat` ADD `isi_surat` TEXT NULL AFTER `perihal`");
        }
        if (!in_array('hari_tanggal', $cols)) {
            DB::statement("ALTER TABLE `surat` ADD `hari_tanggal` VARCHAR(255) NULL AFTER `isi_surat`");
        }
        if (!in_array('waktu', $cols)) {
            DB::statement("ALTER TABLE `surat` ADD `waktu` VARCHAR(255) NULL AFTER `hari_tanggal`");
        }
        if (!in_array('tempat', $cols)) {
            DB::statement("ALTER TABLE `surat` ADD `tempat` VARCHAR(255) NULL AFTER `waktu`");
        }
        if (!in_array('agenda', $cols)) {
            DB::statement("ALTER TABLE `surat` ADD `agenda` TEXT NULL AFTER `tempat`");
        }
        if (!in_array('penutup', $cols)) {
            DB::statement("ALTER TABLE `surat` ADD `penutup` TEXT NULL AFTER `agenda`");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $cols = array_column(DB::select("SHOW COLUMNS FROM `surat`"), 'Field');

        foreach (['penutup', 'agenda', 'tempat', 'waktu', 'hari_tanggal', 'isi_surat'] as $col) {
            if (in_array($col, $cols)) {
                DB::statement("ALTER TABLE `surat` DROP COLUMN `{$col}`");
            }
        }
    }
};

