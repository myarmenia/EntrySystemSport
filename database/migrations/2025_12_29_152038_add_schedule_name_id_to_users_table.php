<?php

// php artisan make:migration add_schedule_name_id_to_users_table --table=users

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('schedule_name_id')->nullable()->after('id');

            $table->foreign('schedule_name_id')
                ->references('id')
                ->on('schedule_names')
                ->nullOnDelete(); // schedule ջնջվելու դեպքում user.schedule_name_id = null
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['schedule_name_id']);
            $table->dropColumn('schedule_name_id');
        });
    }
};
