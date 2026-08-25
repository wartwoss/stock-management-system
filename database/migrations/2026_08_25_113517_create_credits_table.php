
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('credits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')
                ->unique()
                ->constrained('sales')
                ->restrictOnDelete();
            $table->foreignId('customer_id')
                ->constrained('customers')
                ->restrictOnDelete();
            $table->decimal('total_amount', 12, 2);
            $table->decimal('down_payment', 12, 2)
                ->default(0);
            $table->decimal('remaining_debt', 12, 2);
            $table->decimal('installment_amount', 12, 2);
            $table->unsignedInteger('number_of_payments');
            $table->unsignedInteger('payments_made')
                ->default(0);
            $table->date('first_due_date');
            $table->date('next_due_date')
                ->nullable();
            $table->string('status', 30)
                ->default('active');
            $table->index('next_due_date');
            $table->index('status');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('credits');
    }
};