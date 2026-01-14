<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('people', function (Blueprint $table) {
            $table->unsignedBigInteger('trainer_id')->nullable()->after('package_id'); // տեղը կարող ես փոխել

            $table->foreign('trainer_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete(); // trainer user ջնջվելու դեպքում trainer_id = null
        });
    }

    public function down(): void
    {
        Schema::table('people', function (Blueprint $table) {
            $table->dropForeign(['trainer_id']);
            $table->dropColumn('trainer_id');
        });
    }
};
