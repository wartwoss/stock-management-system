<?php
namespace App\Domain\PaymentMonitoring\Controllers;
use App\Http\Controllers\Controller;
use App\Domain\PaymentMonitoring\Requests\UpdatePaymentMonitoringSettingRequest;
use App\Domain\PaymentMonitoring\Services\PaymentMonitoringService;
use Illuminate\Http\JsonResponse;
class PaymentMonitoringController extends Controller
{
    public function __construct(
        private PaymentMonitoringService $service
    ) {
    }
    public function settings(): JsonResponse
    {
        return response()->json(
            $this->service->getSettings()
        );
    }
    public function updateSettings(
        UpdatePaymentMonitoringSettingRequest $request
    ): JsonResponse {
        return response()->json(
            $this->service->updateSettings(
                $request->validated()
            )
        );
    }
    public function runNow(): JsonResponse
    {
        return response()->json(
            $this->service->runCheck()
        );
    }
    public function status(): JsonResponse
    {
        return response()->json(
            $this->service->getStatus()
        );
    }
}
