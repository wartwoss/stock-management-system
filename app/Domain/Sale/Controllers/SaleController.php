<?php
namespace App\Domain\Sale\Controllers;
use App\Http\Controllers\Controller;
use App\Domain\Sale\Requests\StoreSaleRequest;
use App\Domain\Sale\Services\SaleService;
use Illuminate\Http\JsonResponse;
class SaleController extends Controller
{
    public function __construct(
        private SaleService $saleService
    ) {
    }
    public function index(): JsonResponse
    {
        return response()->json(
            $this->saleService->getAll()
        );
    }
    public function show(
        int $sale
    ): JsonResponse {
        return response()->json(
            $this->saleService->findById($sale)
        );
    }
    public function store(
        StoreSaleRequest $request
    ): JsonResponse {
        $sale = $this->saleService->create(
            $request->validated()
        );
        return response()->json(
            $sale,
            201
        );
    }
}