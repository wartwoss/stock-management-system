<?php

namespace App\Domain\Payment\Controllers;
use App\Http\Controllers\Controller;
use App\Domain\Payment\Requests\StorePaymentRequest;
use App\Domain\Payment\Services\PaymentService;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    public function __construct(
        private PaymentService $paymentService
    ) {
    }
    public function index(): JsonResponse
    {
        return response()->json(
            $this->paymentService->getAll()
        );
    }
    public function show(
        int $payment
    ): JsonResponse {
        return response()->json(
            $this->paymentService
                ->findById($payment)
        );
    }
    public function byCredit(
        int $credit
    ): JsonResponse {
        return response()->json(
            $this->paymentService
                ->getByCredit($credit)
        );
    }
    public function store(
        StorePaymentRequest $request,
        int $credit
    ): JsonResponse {
        $payment =
            $this->paymentService->recordPayment(
                $credit,
                $request->validated()
            );
        return response()->json(
            $payment,
            201
        );
    }
}