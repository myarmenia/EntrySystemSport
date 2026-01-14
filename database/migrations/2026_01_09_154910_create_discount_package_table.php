<?php

// database/migrations/xxxx_xx_xx_create_discount_package_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('discount_package', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('discount_id');
            $table->unsignedBigInteger('package_id');

            $table->timestamps();

            // որ նույն զույգը 2 անգամ չգրվի
            $table->unique(['discount_id', 'package_id']);

            // FK-ները խորհուրդ եմ տալիս միացնել, եթե packages/discounts անունները ճիշտ են
            $table->foreign('discount_id')->references('id')->on('discounts')->cascadeOnDelete();
            $table->foreign('package_id')->references('id')->on('packages')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discount_package');
    }
};
