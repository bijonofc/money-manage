<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('debts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('users')->cascadeOnDelete();
            $table->enum('type', ['owed_to', 'owed_from']);
            $table->string('creditor_name', 255);
            $table->string('creditor_contact', 100)->nullable();
            $table->decimal('principal_amount', 15, 2);
            $table->decimal('paid_amount', 15, 2)->default(0.00);
            $table->decimal('interest_rate', 5, 2)->nullable();
            $table->date('due_date')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'paid', 'overdue', 'cancelled']);
            $table->timestamps();

            $table->index('tenant_id');
            $table->index(['status', 'due_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('debts');
    }
};
