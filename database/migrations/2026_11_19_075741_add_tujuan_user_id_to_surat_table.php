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

        if (!in_array('tujuan_user_id', $cols)) {
            DB::statement("ALTER TABLE `surat` ADD `tujuan_user_id` BIGINT UNSIGNED NULL AFTER `tujuan_disposisi`");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $cols = array_column(DB::select("SHOW COLUMNS FROM `surat`"), 'Field');

        if (in_array('tujuan_user_id', $cols)) {
            DB::statement("ALTER TABLE `surat` DROP COLUMN `tujuan_user_id`");
        }
    }
};
