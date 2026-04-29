<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('transactions')->nullOnDelete();
            $table->enum('transaction_type', ['income', 'expense', 'transfer']);
            $table->decimal('amount', 15, 2);
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->foreignId('from_account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->date('date');
            $table->text('description')->nullable();
            $table->string('reference_number', 100)->nullable();
            $table->enum('payment_method', ['cash', 'card', 'bank_transfer', 'mobile', 'check', 'other'])->nullable();
            $table->boolean('is_recurring')->default(false);
            $table->foreignId('recurring_id')->nullable()->constrained('recurring_transactions')->nullOnDelete();
            $table->json('tags')->nullable();
            $table->string('attachment_path', 255)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'date']);
            $table->index('category_id');
            $table->index('account_id');
            $table->index('transaction_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
