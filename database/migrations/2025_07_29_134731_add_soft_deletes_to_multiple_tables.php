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
        Schema::table('multiple_tables', function (Blueprint $table) {
            Schema::table('entry_codes', function (Blueprint $table) {
                $table->softDeletes()->after('type');
            });

            Schema::table('users', function (Blueprint $table) {
                $table->softDeletes()->after('remember_token');
            });
            Schema::table('clients', function (Blueprint $table) {
                $table->softDeletes()->after('working_time');
            });
             Schema::table('schedule_details', function (Blueprint $table) {
                $table->softDeletes()->after('break_end_time');
            });
             Schema::table('departments', function (Blueprint $table) {
                $table->softDeletes()->after('name');
            });
             Schema::table('schedule_names', function (Blueprint $table) {
                $table->softDeletes()->after('status');
            });
             Schema::table('people', function (Blueprint $table) {
                $table->softDeletes()->after('type');
            });
             Schema::table('person_permissions', function (Blueprint $table) {
                $table->softDeletes()->after('status');
            });




        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('multiple_tables', function (Blueprint $table) {
            //
        });
    }
};
