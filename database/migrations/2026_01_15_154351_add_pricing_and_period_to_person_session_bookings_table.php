<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('person_session_bookings', function (Blueprint $table) {
            // ✅ session start/end as datetime (exact slot datetime)
            $table->dateTime('session_start_time')->nullable()->after('end_time');
            $table->dateTime('session_end_time')->nullable()->after('session_start_time');

            // ✅ pricing snapshot
            $table->unsignedInteger('package_months')->nullable()->after('session_end_time');
            $table->unsignedInteger('package_price_amd')->nullable()->after('package_months');
            $table->unsignedInteger('duration_price_amd')->nullable()->after('package_price_amd');
            $table->unsignedInteger('duration_total_amd')->nullable()->after('duration_price_amd');
            $table->unsignedInteger('total_price_amd')->nullable()->after('duration_total_amd');
        });
    }

    public function down(): void
    {
        Schema::table('person_session_bookings', function (Blueprint $table) {
            $table->dropColumn([
                'session_start_time',
                'session_end_time',
                'package_months',
                'package_price_amd',
                'duration_price_amd',
                'duration_total_amd',
                'total_price_amd',
            ]);
        });
    }
};
