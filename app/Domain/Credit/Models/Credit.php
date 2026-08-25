<?php
namespace App\Domain\Credit\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Domain\Sale\Models\Sale;
use App\Domain\Customer\Models\Customer;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Domain\Payment\Models\Payment;
use App\Domain\Notification\Models\Notification;

class Credit extends Model
{
    protected $table = 'credits';
    public $timestamps = false;
    protected $fillable = [
        'sale_id',
        'customer_id',
        'total_amount',
        'down_payment',
        'remaining_debt',
        'installment_amount',
        'number_of_payments',
        'payments_made',
        'first_due_date',
        'next_due_date',
        'status',
    ];
    protected function casts(): array
    {
        return [
            'sale_id' => 'integer',
            'customer_id' => 'integer',
            'total_amount' => 'decimal:2',
            'down_payment' => 'decimal:2',
            'remaining_debt' => 'decimal:2',
            'installment_amount' => 'decimal:2',
            'number_of_payments' => 'integer',
            'payments_made' => 'integer',
            'first_due_date' => 'date:Y-m-d',
            'next_due_date' => 'date:Y-m-d',
        ];
    }
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(
            Payment::class,
            'credit_id'
        );
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(
            Notification::class,
            'credit_id'
        );
    }
}
