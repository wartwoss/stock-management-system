<?php
namespace App\Domain\Sale\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Domain\Appliance\Models\Appliance;
use App\Domain\Storage\Models\Storage;
use App\Domain\Customer\Models\Customer;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Domain\Credit\Models\Credit;



class Sale extends Model
{
    protected $table = 'sales';
    public $timestamps = false;
    protected $fillable = [
        'appliance_id',
        'storage_id',
        'customer_id',
        'quantity',
        'selling_price',
        'total_price',
        'payment_type',
        'sale_date',
    ];
    protected function casts(): array
    {
        return [
            'appliance_id' => 'integer',
            'storage_id' => 'integer',
            'customer_id' => 'integer',
            'quantity' => 'integer',
            'selling_price' => 'decimal:2',
            'total_price' => 'decimal:2',
            'sale_date' => 'date:Y-m-d',
        ];
    }
    public function appliance(): BelongsTo
    {
        return $this->belongsTo(Appliance::class);
    }
    public function storage(): BelongsTo
    {
        return $this->belongsTo(Storage::class);
    }
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }


    public function credit(): HasOne
    {
        return $this->hasOne(
            Credit::class,
            'sale_id'
        );
    }
}