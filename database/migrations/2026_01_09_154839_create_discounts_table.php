<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('client_id')->nullable(); // optional՝ եթե client-ներով ես ֆիլտրում

            $table->string('name');
            $table->enum('type', ['percent', 'fixed'])->default('percent'); // % կամ fixed AMD
            $table->decimal('value', 10, 2); // օրինակ 10.00 (%) կամ 500.00 (AMD)

            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();

            $table->boolean('status')->default(true); // 1 ակտիվ, 0 պասիվ

            $table->timestamps();
            $table->softDeletes();

            // Եթե client_id-ն պիտի պարտադիր FK լինի՝ բացիր
            // $table->foreign('client_id')->references('id')->on('clients')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
};
