<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recurring_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('users')->cascadeOnDelete();
            $table->string('name', 100);
            $table->enum('transaction_type', ['income', 'expense']);
            $table->decimal('amount', 15, 2);
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->enum('frequency', ['daily', 'weekly', 'bi_weekly', 'monthly', 'quarterly', 'yearly']);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->tinyInteger('day_of_month')->nullable();
            $table->tinyInteger('day_of_week')->nullable();
            $table->boolean('is_active')->default(true);
            $table->date('last_processed_date')->nullable();
            $table->date('next_due_date')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'is_active']);
            $table->index(['next_due_date', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recurring_transactions');
    }
};
