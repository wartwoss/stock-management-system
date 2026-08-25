<?php
namespace App\Domain\Storage\Models;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Domain\Inventory\Models\Inventory;
use App\Domain\Sale\Models\Sale;


class Storage extends Model
{
    protected $table = 'storages';
    public $timestamps = false;
    protected $fillable = [
        'name',
        'location',
    ];

    public function inventories(): HasMany
    {
        return $this->hasMany(
            Inventory::class,
            'storage_id'
        );
    }

    public function sales(): HasMany
    {
        return $this->hasMany(
            Sale::class,
            'storage_id'
        );
    }
}