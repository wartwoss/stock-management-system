<?php
namespace App\Domain\Credit\Controllers;
use App\Http\Controllers\Controller;
use App\Domain\Credit\Requests\StoreCreditRequest;
use App\Domain\Credit\Requests\UpdateCreditRequest;
use App\Domain\Credit\Services\CreditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
class CreditController extends Controller
{
    public function __construct(
        private CreditService $creditService
    ) {
    }
    public function index(): JsonResponse
    {
        return response()->json(
            $this->creditService->getAll()
        );
    }
    public function show(
        int $credit
    ): JsonResponse {
        return response()->json(
            $this->creditService
                ->findById($credit)
        );
    }
    public function store(
        StoreCreditRequest $request
    ): JsonResponse {
        $credit =
            $this->creditService->create(
                $request->validated()
            );
        return response()->json(
            $credit,
            201
        );
    }
    public function update(
        UpdateCreditRequest $request,
        int $credit
    ): JsonResponse {
        return response()->json(
            $this->creditService->update(
                $credit,
                $request->validated()
            )
        );
    }
    public function summary(
        int $credit
    ): JsonResponse {
        return response()->json(
            $this->creditService
                ->getSummary($credit)
        );
    }
    public function upcoming(
        Request $request
    ): JsonResponse {
        $days = max(
            1,
            min(
                (int) $request->query('days', 7),
                365
            )
        );
        return response()->json(
            $this->creditService
                ->getUpcoming($days)
        );
    }
    public function overdue(): JsonResponse
    {
        return response()->json(
            $this->creditService
                ->getOverdue()
        );
    }
    public function completed(): JsonResponse
    {
        return response()->json(
            $this->creditService
                ->getCompleted()
        );
    }
}