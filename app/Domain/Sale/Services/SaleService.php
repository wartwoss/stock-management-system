<?php
namespace App\Domain\Sale\Services;
use App\Domain\Sale\Models\Sale;
use App\Domain\Inventory\Services\InventoryService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
class SaleService
{
    public function __construct(
        private InventoryService $inventoryService
    ) {
    }
    public function getAll(): Collection
    {
        return Sale::with([
            'appliance' => fn($q) => $q->withTrashed(),
            'storage',
            'customer',
        ])
            ->orderByDesc('id')
            ->get();
    }
    public function findById(int $id): Sale
    {
        return Sale::with([
            'appliance' => fn($q) => $q->withTrashed(),
            'storage',
            'customer',
        ])
            ->findOrFail($id);
    }
    public function create(array $data): Sale
    {
        return DB::transaction(function () use ($data) {
            $totalPrice = round(
                (float) $data['selling_price']
                * (int) $data['quantity'],
                2
            );
            $this->inventoryService->decreaseStock(
                $data['appliance_id'],
                $data['storage_id'],
                $data['quantity']
            );
            $sale = Sale::create([
                'appliance_id' => $data['appliance_id'],
                'storage_id' => $data['storage_id'],
                'customer_id' =>
                    $data['customer_id'] ?? null,
                'quantity' => $data['quantity'],
                'selling_price' =>
                    $data['selling_price'],
                'total_price' =>
                    $totalPrice,
                'payment_type' =>
                    $data['payment_type'],
                'warranty_months' =>
                    $data['warranty_months'] ?? null,
                'sale_date' =>
                    $data['sale_date'],
                'currency' =>
                    $data['currency'] ?? 'USD',
                'exchange_rate_per_100' =>
                    $data['exchange_rate_per_100'] ?? null,
            ]);
            return $sale->load([
                'appliance' => fn($q) => $q->withTrashed(),
                'storage',
                'customer',
            ]);
        });
    }
}
