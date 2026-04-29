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
        Schema::create('role_accesses', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('role_id',false);
            $table->string('resource',100);
            $table->char('role_access',1)->default('N')->comment('Y=Yes,N=No');
            $table->unique(['role_id','resource'],'role_resource','BTREE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_accesses');
    }
};
