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
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'google_id')) {
                $table->string('google_id', 191)->nullable()->unique()->after('remember_token');
            }
            if (! Schema::hasColumn('users', 'is_sso')) {
                $table->char('is_sso', 1)->default('N')->after('status');
            }
            // Make contact_no nullable for self-registration and SSO users
            $table->string('contact_no', 191)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'google_id')) {
                $table->dropColumn('google_id');
            }
            if (Schema::hasColumn('users', 'is_sso')) {
                $table->dropColumn('is_sso');
            }
            $table->string('contact_no', 191)->nullable(false)->change();
        });
    }
};
