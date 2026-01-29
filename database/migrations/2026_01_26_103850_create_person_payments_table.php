<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('person_payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('person_id')->constrained('people')->cascadeOnDelete();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();

            $table->enum('payment_method', ['cash', 'cashless', 'credit']);
            $table->string('payment_bank', 50)->nullable();

            $table->unsignedInteger('amount_amd'); // որքան է վճարվել
            $table->string('currency', 5)->default('AMD');

            $table->enum('status', ['paid', 'pending'])->default('paid');

            $table->timestamp('paid_at')->nullable();

            $table->timestamps();

            // ✅ useful indexes
            $table->index(['client_id', 'person_id']);
            $table->index(['client_id', 'status']);
            $table->index(['paid_at']);
        });

        // ✅ DB-level rule (աշխատում է Postgres-ում)
        // cash -> payment_bank must be null
        // cashless/credit -> payment_bank must be not null
        DB::statement("
            ALTER TABLE person_payments
            ADD CONSTRAINT person_payments_bank_check
            CHECK (
                (payment_method = 'cash' AND payment_bank IS NULL)
                OR
                (payment_method IN ('cashless','credit') AND payment_bank IS NOT NULL)
            )
        ");
    }

    public function down(): void
    {
        // drop constraint for postgres (safe)
        try {
            DB::statement("ALTER TABLE person_payments DROP CONSTRAINT IF EXISTS person_payments_bank_check");
        } catch (\Throwable $e) {}

        Schema::dropIfExists('person_payments');
    }
};
