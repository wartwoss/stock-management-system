<?php
namespace App\Domain\Inventory\Services;
use App\Domain\Inventory\Models\Inventory;
use App\Domain\Appliance\Models\Appliance;
use App\Domain\Storage\Models\Storage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
class InventoryService
{
    public function getAll(): Collection
    {
        return Inventory::with(['appliance', 'storage'])->whereHas('appliance')->get();
    }
    public function findById(int $id): Inventory
    {
        return Inventory::with([
            'appliance',
            'storage'
        ])->findOrFail($id);
    }
    public function stockIn(array $data): Inventory
    {
        return DB::transaction(function () use ($data) {
            $inventory = Inventory::query()
                ->where(
                    'appliance_id',
                    $data['appliance_id']
                )
                ->where(
                    'storage_id',
                    $data['storage_id']
                )
                ->lockForUpdate()
                ->first();
            if (!$inventory) {
                $inventory = Inventory::create([
                    'appliance_id' =>
                        $data['appliance_id'],
                    'storage_id' =>
                        $data['storage_id'],
                    'total_purchased' =>
                        $data['quantity'],
                    'quantity_in_stock' =>
                        $data['quantity'],
                    'quantity_sold' => 0,
                ]);
            } else {
                $inventory->total_purchased +=
                    $data['quantity'];
                $inventory->quantity_in_stock +=
                    $data['quantity'];
                $inventory->save();
            }
            return $inventory
                ->refresh()
                ->load([
                    'appliance',
                    'storage'
                ]);
        });
    }
    public function adjust(
        int $id,
        int $adjustment
    ): Inventory {
        return DB::transaction(
            function () use ($id, $adjustment) {
                $inventory = Inventory::query()
                    ->lockForUpdate()
                    ->findOrFail($id);
                $newQuantity =
                    $inventory->quantity_in_stock
                    + $adjustment;
                if ($newQuantity < 0) {
                    throw ValidationException::withMessages([
                        'adjustment' =>
                            'Stock cannot go below zero.',
                    ]);
                }
                $inventory->quantity_in_stock =
                    $newQuantity;
                $inventory->save();
                return $inventory
                    ->refresh()
                    ->load([
                        'appliance',
                        'storage'
                    ]);
            }
        );
    }
    public function getByAppliance(
        int $applianceId
    ): Collection {
        Appliance::findOrFail($applianceId);
        return Inventory::with('storage')
            ->where(
                'appliance_id',
                $applianceId
            )
            ->get();
    }
    public function getByStorage(
        int $storageId
    ): Collection {
        Storage::findOrFail($storageId);
        return Inventory::with('appliance')
            ->where(
                'storage_id',
                $storageId
            )
            ->get();
    }
    public function checkAvailability(
        int $applianceId,
        int $storageId,
        int $quantity
    ): bool {
        $inventory = Inventory::query()
            ->where(
                'appliance_id',
                $applianceId
            )
            ->where(
                'storage_id',
                $storageId
            )
            ->first();
        if (!$inventory) {
            return false;
        }
        return $inventory->quantity_in_stock
            >= $quantity;
    }
    public function decreaseStock(
        int $applianceId,
        int $storageId,
        int $quantity
    ): Inventory {
        return DB::transaction(
            function () use (
                $applianceId,
                $storageId,
                $quantity
            ) {
                $inventory = Inventory::query()
                    ->where(
                        'appliance_id',
                        $applianceId
                    )
                    ->where(
                        'storage_id',
                        $storageId
                    )
                    ->lockForUpdate()
                    ->firstOrFail();
                if (
                    $inventory->quantity_in_stock
                    < $quantity
                ) {
                    throw ValidationException::withMessages([
                        'quantity' =>
                            'Not enough stock available.',
                    ]);
                }
                $inventory->quantity_in_stock -=
                    $quantity;
                $inventory->quantity_sold +=
                    $quantity;
                $inventory->save();
                return $inventory->refresh();
            }
        );
    }
}
