<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appliance_id')
                ->constrained('appliances')
                ->restrictOnDelete();
            $table->foreignId('storage_id')
                ->constrained('storages')
                ->restrictOnDelete();
            $table->foreignId('customer_id')
                ->nullable()
                ->constrained('customers')
                ->restrictOnDelete();
            $table->unsignedInteger('quantity');
            $table->decimal('selling_price', 12, 2);
            $table->decimal('total_price', 12, 2);
            $table->string('payment_type', 20);
            $table->date('sale_date');
            $table->index('sale_date');
            $table->index('payment_type');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};