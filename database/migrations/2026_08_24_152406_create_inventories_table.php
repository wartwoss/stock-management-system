<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appliance_id')
                ->constrained('appliances')
                ->restrictOnDelete();
            $table->foreignId('storage_id')
                ->constrained('storages')
                ->restrictOnDelete();
            $table->unsignedInteger('total_purchased')->default(0);
            $table->unsignedInteger('quantity_in_stock')->default(0);
            $table->unsignedInteger('quantity_sold')->default(0);
            $table->unique([
                'appliance_id',
                'storage_id'
            ]);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};