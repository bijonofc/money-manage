<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('users')->cascadeOnDelete();
            $table->string('name', 100);
            $table->enum('account_type', ['cash', 'bank', 'mobile', 'credit_card', 'other']);
            $table->string('account_number', 50)->nullable();
            $table->decimal('balance', 15, 2)->default(0.00);
            $table->string('currency', 3)->default('BDT');
            $table->json('meta')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('tenant_id');
            $table->index('account_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
