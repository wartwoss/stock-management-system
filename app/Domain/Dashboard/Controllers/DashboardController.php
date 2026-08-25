<?php
namespace App\Domain\Dashboard\Controllers;
use App\Http\Controllers\Controller;
use App\Domain\Dashboard\Requests\DashboardFilterRequest;
use App\Domain\Dashboard\Services\DashboardService;
use Illuminate\Http\JsonResponse;
class DashboardController extends Controller
{
    public function __construct(
        private DashboardService $dashboardService
    ) {
    }
    public function summary(
        DashboardFilterRequest $request
    ): JsonResponse {
        return response()->json(
            $this->dashboardService
                ->getSummary(
                    $request->validated()
                )
        );
    }
    public function inventory(
        DashboardFilterRequest $request
    ): JsonResponse {
        return response()->json(
            $this->dashboardService
                ->getInventorySummary(
                    $request->validated()
                )
        );
    }
    public function sales(
        DashboardFilterRequest $request
    ): JsonResponse {
        return response()->json(
            $this->dashboardService
                ->getSalesSummary(
                    $request->validated()
                )
        );
    }
    public function credits(): JsonResponse
    {
        return response()->json(
            $this->dashboardService
                ->getCreditSummary()
        );
    }
}