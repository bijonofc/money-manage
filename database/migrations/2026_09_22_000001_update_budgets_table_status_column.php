<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('budgets', 'is_active') && ! Schema::hasColumn('budgets', 'status')) {
            Schema::table('budgets', function (Blueprint $table) {
                $table->char('status', 1)->default('A')->comment('A=Active,I=Inactive')->after('end_date');
            });

            DB::statement("UPDATE budgets SET status = IF(is_active = 1, 'A', 'I')");

            Schema::table('budgets', function (Blueprint $table) {
                $table->dropColumn('is_active');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('budgets', 'status') && ! Schema::hasColumn('budgets', 'is_active')) {
            Schema::table('budgets', function (Blueprint $table) {
                $table->boolean('is_active')->default(true)->after('end_date');
            });

            DB::statement("UPDATE budgets SET is_active = IF(status = 'A', 1, 0)");

            Schema::table('budgets', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
};
