<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'payment_monitoring_settings',
            function (Blueprint $table) {
                $table->id();
                $table->boolean('enabled')
                    ->default(true);
                $table->time('check_time')
                    ->default('20:00:00');
                $table->unsignedTinyInteger(
                    'days_before_due'
                )->default(3);
                $table->timestamp(
                    'last_checked_at'
                )->nullable();
                $table->timestamps();
            }
        );
    }
    public function down(): void
    {
        Schema::dropIfExists(
            'payment_monitoring_settings'
        );
    }
};
