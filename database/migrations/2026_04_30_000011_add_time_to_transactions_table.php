<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('transactions') && !Schema::hasColumn('transactions', 'time')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->time('time')->nullable()->after('date');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('transactions') && Schema::hasColumn('transactions', 'time')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->dropColumn('time');
            });
        }
    }
};
