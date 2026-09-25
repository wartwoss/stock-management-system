<?php
namespace App\Domain\Appliance\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Domain\Inventory\Models\Inventory;

use App\Domain\Sale\Models\Sale;


class Appliance extends Model
{
    use SoftDeletes;
    protected $table = 'appliances';
    public $timestamps = false;
    protected $fillable = [
        'name',
        'category',
        'brand',
        'purchase_price',
        'date_added',
    ];
    protected function casts(): array
    {
        return [
            'purchase_price' => 'decimal:2',
            'date_added' => 'date:Y-m-d',
        ];
    }

    public function inventories(): HasMany
    {
        return $this->hasMany(
            Inventory::class,
            'appliance_id'
        );
    }

    public function sales(): HasMany
    {
        return $this->hasMany(
            Sale::class,
            'appliance_id'
        );
    }
}
