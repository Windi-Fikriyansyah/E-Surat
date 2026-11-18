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

        if (!in_array('penerima_surat', $cols)) {
            DB::statement("ALTER TABLE `surat` ADD `penerima_surat` VARCHAR(255) NULL AFTER `asal_surat`");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $cols = array_column(DB::select("SHOW COLUMNS FROM `surat`"), 'Field');

        if (in_array('penerima_surat', $cols)) {
            DB::statement("ALTER TABLE `surat` DROP COLUMN `penerima_surat`");
        }
    }
};

