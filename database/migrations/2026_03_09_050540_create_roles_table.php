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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('title',100);
            $table->string('slug',100)->unique();
            $table->string('description')->nullable();
            $table->string('display_as',100)->nullable();
            $table->char('is_super',1)->default('N')->comment('Y=Yes,N=No');
            $table->char('status',1)->default('A')->comment('A=Active,I=Inactive');
            $table->unsignedInteger('added_by')->default(0)->comment('who added the role');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
