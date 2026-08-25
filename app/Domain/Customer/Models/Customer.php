<?php
namespace App\Domain\Customer\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Domain\Sale\Models\Sale;

use App\Domain\Credit\Models\Credit;


class Customer extends Model
{
    protected $table = 'customers';
    public $timestamps = false;
    protected $fillable = [
        'name',
        'phone_number',
        'description',
    ];
    public function sales(): HasMany
    {
        return $this->hasMany(
            Sale::class,
            'customer_id'
        );
    }

    public function credits(): HasMany
    {
        return $this->hasMany(
            Credit::class,
            'customer_id'
        );
    }
}
