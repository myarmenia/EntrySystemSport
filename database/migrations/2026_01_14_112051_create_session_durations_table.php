<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('session_durations', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('minutes'); // 60, 90, 120
            $table->string('title')->nullable();              // "1 ժամ", "2 ժամ"
            $table->unsignedInteger('price_amd');   // trainer-ի գինը տվյալ duration-ի համար

            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('session_durations');
    }
};
