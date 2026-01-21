<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schedule_department_people', function (Blueprint $table) {
            // ✅ session_duration_id (nullable, քանի որ միշտ չէ, որ կլինի)
            $table->foreignId('session_duration_id')
                ->nullable()
                ->after('schedule_name_id') // կարող ես տեղը փոխել
                ->constrained('session_durations')
                ->nullOnDelete(); // եթե duration-ը ջնջվի, դառնա null
        });
    }

    public function down(): void
    {
        Schema::table('schedule_department_people', function (Blueprint $table) {
            $table->dropForeign(['session_duration_id']);
            $table->dropColumn('session_duration_id');
        });
    }
};
