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
        Schema::table('sales', function (Blueprint $table) {
            $table->string('currency', 10)->default('USD');
            $table->decimal('exchange_rate_per_100', 12, 2)->nullable();
        });

        Schema::table('credits', function (Blueprint $table) {
            $table->string('currency', 10)->default('USD');
            $table->decimal('exchange_rate_per_100', 12, 2)->nullable();
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->string('currency', 10)->default('USD');
            $table->decimal('exchange_rate_per_100', 12, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['currency', 'exchange_rate_per_100']);
        });

        Schema::table('credits', function (Blueprint $table) {
            $table->dropColumn(['currency', 'exchange_rate_per_100']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['currency', 'exchange_rate_per_100']);
        });
    }
};
