<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('savings_contributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('goal_id')->constrained('savings_goals')->cascadeOnDelete();
            $table->foreignId('transaction_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 15, 2);
            $table->string('note', 255)->nullable();
            $table->timestamps();

            $table->index('goal_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('savings_contributions');
    }
};
