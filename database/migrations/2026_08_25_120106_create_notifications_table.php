
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('credit_id')
                ->constrained('credits')
                ->restrictOnDelete();
            $table->string('type', 30);
            $table->string('title', 255);
            $table->text('message');
            $table->boolean('is_read')
                ->default(false);
            $table->timestamp('resolved_at')
                ->nullable();
            $table->timestamps();
            $table->index('type');
            $table->index('is_read');
            $table->index('resolved_at');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};