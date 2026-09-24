<?php
namespace App\Domain\Payment\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Domain\Credit\Models\Credit;
class Payment extends Model
{
    protected $table = 'payments';
    public $timestamps = false;
    protected $fillable = [
        'credit_id',
        'amount',
        'payment_date',
        'currency',
        'exchange_rate_per_100',
    ];
    protected function casts(): array
    {
        return [
            'credit_id' => 'integer',
            'amount' => 'decimal:2',
            'payment_date' => 'date:Y-m-d',
            'exchange_rate_per_100' => 'decimal:2',
        ];
    }
    public function credit(): BelongsTo
    {
        return $this->belongsTo(
            Credit::class,
            'credit_id'
        );
    }
}