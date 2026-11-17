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
        // 1. Rename table surat_masuk to surat if surat_masuk exists
        $hasSuratMasuk = !empty(DB::select("SHOW TABLES LIKE 'surat_masuk'"));
        $hasSurat = !empty(DB::select("SHOW TABLES LIKE 'surat'"));

        if ($hasSuratMasuk && !$hasSurat) {
            DB::statement("RENAME TABLE `surat_masuk` TO `surat`");
        }

        // 2. Check columns of table surat safely without querying generation_expression
        $cols = array_column(DB::select("SHOW COLUMNS FROM `surat`"), 'Field');

        if (!in_array('jenis_surat', $cols)) {
            DB::statement("ALTER TABLE `surat` ADD `jenis_surat` ENUM('Masuk', 'Keluar') DEFAULT 'Masuk' AFTER `id`");
        }

        if (!in_array('tujuan_surat', $cols)) {
            DB::statement("ALTER TABLE `surat` ADD `tujuan_surat` VARCHAR(255) NULL AFTER `asal_surat`");
        }

        // 3. Make columns nullable for flexibility between Surat Masuk and Surat Keluar
        DB::statement("ALTER TABLE `surat` MODIFY `asal_surat` VARCHAR(255) NULL");
        DB::statement("ALTER TABLE `surat` MODIFY `tanggal_terima` DATE NULL");
        DB::statement("ALTER TABLE `surat` MODIFY `status_disposisi` ENUM('Belum Didisposisikan', 'Sudah Didisposisikan', 'Selesai') NULL");
        DB::statement("ALTER TABLE `surat` MODIFY `tujuan_disposisi` VARCHAR(255) NULL");

        // 4. Backfill tujuan_surat from tujuan_disposisi if empty
        DB::statement("UPDATE `surat` SET `tujuan_surat` = `tujuan_disposisi` WHERE `tujuan_surat` IS NULL AND `tujuan_disposisi` IS NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $hasSurat = !empty(DB::select("SHOW TABLES LIKE 'surat'"));
        $hasSuratMasuk = !empty(DB::select("SHOW TABLES LIKE 'surat_masuk'"));

        if ($hasSurat && !$hasSuratMasuk) {
            DB::statement("RENAME TABLE `surat` TO `surat_masuk`");
        }
    }
};
