<?php
namespace App\Domain\Inventory\Controllers;
use App\Http\Controllers\Controller;
use App\Domain\Inventory\Requests\StockInInventoryRequest;
use App\Domain\Inventory\Requests\AdjustInventoryRequest;
use App\Domain\Inventory\Services\InventoryService;
use Illuminate\Http\JsonResponse;
class InventoryController extends Controller
{
    public function __construct(
        private InventoryService $inventoryService
    ) {
    }
    public function index(): JsonResponse
    {
        return response()->json(
            $this->inventoryService->getAll()
        );
    }
    public function show(
        int $inventory
    ): JsonResponse {
        return response()->json(
            $this->inventoryService
                ->findById($inventory)
        );
    }
    public function stockIn(
        StockInInventoryRequest $request
    ): JsonResponse {
        $inventory =
            $this->inventoryService->stockIn(
                $request->validated()
            );
        return response()->json(
            $inventory
        );
    }
    public function adjust(
        AdjustInventoryRequest $request,
        int $inventory
    ): JsonResponse {
        $inventory =
            $this->inventoryService->adjust(
                $inventory,
                $request->validated()['adjustment']
            );
        return response()->json(
            $inventory
        );
    }
    public function byAppliance(
        int $appliance
    ): JsonResponse {
        return response()->json(
            $this->inventoryService
                ->getByAppliance($appliance)
        );
    }
    public function byStorage(
        int $storage
    ): JsonResponse {
        return response()->json(
            $this->inventoryService
                ->getByStorage($storage)
        );
    }
}