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
        Schema::table('passwordless_logins', function (Blueprint $table) {
            $table->integer('use_count')->default(0)->after('used_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('passwordless_logins', function (Blueprint $table) {
            $table->dropColumn('use_count');
        });
    }
};
