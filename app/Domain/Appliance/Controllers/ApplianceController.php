<?php
namespace App\Domain\Appliance\Controllers;
use App\Http\Controllers\Controller;
use App\Domain\Appliance\Requests\StoreApplianceRequest;
use App\Domain\Appliance\Requests\UpdateApplianceRequest;
use App\Domain\Appliance\Services\ApplianceService;
use Illuminate\Http\JsonResponse;
class ApplianceController extends Controller
{
    public function __construct(
        private ApplianceService $applianceService
    ) {
    }
    public function index(): JsonResponse
    {
        $appliances = $this->applianceService->getAll();
        return response()->json($appliances);
    }
    public function show(int $appliance): JsonResponse
    {
        $appliance = $this->applianceService
            ->findById($appliance);
        return response()->json($appliance);
    }
    public function store(
        StoreApplianceRequest $request
    ): JsonResponse {
        $appliance = $this->applianceService->create(
            $request->validated()
        );
        return response()->json(
            $appliance,
            201
        );
    }
    public function update(
        UpdateApplianceRequest $request,
        int $appliance
    ): JsonResponse {
        $appliance = $this->applianceService->update(
            $appliance,
            $request->validated()
        );
        return response()->json($appliance);
    }
    public function destroy(int $appliance): JsonResponse
    {
        $this->applianceService->delete($appliance);
        return response()->json([
            'message' => 'Appliance deleted successfully.',
        ]);
    }
}