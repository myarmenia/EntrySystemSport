<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // ✅ Drop unique index only if it exists (MySQL safe)
        if (Schema::hasTable('packages')) {
            $indexes = DB::select("SHOW INDEX FROM `packages` WHERE `Key_name` = 'packages_months_unique'");

            if (!empty($indexes)) {
                Schema::table('packages', function (Blueprint $table) {
                    $table->dropUnique('packages_months_unique');
                });
            }
        }
    }

    public function down(): void
    {
        // ✅ Restore unique index only if it doesn't exist
        if (Schema::hasTable('packages')) {
            $indexes = DB::select("SHOW INDEX FROM `packages` WHERE `Key_name` = 'packages_months_unique'");

            if (empty($indexes)) {
                Schema::table('packages', function (Blueprint $table) {
                    $table->unique('months', 'packages_months_unique');
                });
            }
        }
    }
};
