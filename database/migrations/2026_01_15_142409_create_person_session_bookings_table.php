<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('person_session_bookings', function (Blueprint $table) {
            $table->id();

            // relations
            $table->foreignId('client_id')
                ->constrained('clients')
                ->cascadeOnDelete();

            $table->foreignId('person_id')
                ->constrained('people')
                ->cascadeOnDelete();

            // մարզիչը user է
            $table->foreignId('trainer_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('schedule_name_id')
                ->nullable()
                ->constrained('schedule_names')
                ->nullOnDelete();

            $table->foreignId('department_id')
                ->nullable()
                ->constrained('departments')
                ->nullOnDelete();

            $table->foreignId('session_duration_id')
                ->nullable()
                ->constrained('session_durations')
                ->nullOnDelete();

            // ✅ NEW: շաբաթվա օր
            // օրինակ՝ Monday, Tuesday, ...
            $table->string('day');

            // time range
            $table->time('start_time');
            $table->time('end_time');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('person_session_bookings');
    }
};
