<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();

            $table->string('name');              // օրինակ՝ "1 ամիս", "3 ամիս"
            $table->unsignedSmallInteger('months'); // 1,3,12
            $table->unsignedInteger('price_amd');   // 10000,20000,40000

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            //$table->unique('months'); // որ ամիսներով կրկնվի չլինի
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
