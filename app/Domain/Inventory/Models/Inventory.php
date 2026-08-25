
<?php
namespace App\Domain\Inventory\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Domain\Appliance\Models\Appliance;
use App\Domain\Storage\Models\Storage;

class Inventory extends Model
{
    protected $table = 'inventories';
    public $timestamps = false;
    protected $fillable = [
        'appliance_id',
        'storage_id',
        'total_purchased',
        'quantity_in_stock',
        'quantity_sold',
    ];
    protected function casts(): array
    {
        return [
            'appliance_id' => 'integer',
            'storage_id' => 'integer',
            'total_purchased' => 'integer',
            'quantity_in_stock' => 'integer',
            'quantity_sold' => 'integer',
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
}