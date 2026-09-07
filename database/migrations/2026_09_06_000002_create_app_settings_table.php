<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('group_slug', 100)->default('general_settings')->index();
            $table->string('s_key', 150)->index();
            $table->longText('s_val')->nullable();
            $table->string('s_type', 50)->default('string');
            $table->timestamps();

            $table->index(['tenant_id', 'group_slug', 's_key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_settings');
    }
};
