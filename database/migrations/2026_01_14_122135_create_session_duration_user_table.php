<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('session_duration_user', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // trainer user
            $table->foreignId('session_duration_id')->constrained('session_durations')->cascadeOnDelete();

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->unique(['user_id', 'session_duration_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('session_duration_user');
    }
};
