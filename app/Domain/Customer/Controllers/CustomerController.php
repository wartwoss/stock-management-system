<?php
namespace App\Domain\Customer\Controllers;
use App\Http\Controllers\Controller;
use App\Domain\Customer\Requests\StoreCustomerRequest;
use App\Domain\Customer\Requests\UpdateCustomerRequest;
use App\Domain\Customer\Services\CustomerService;
use Illuminate\Http\JsonResponse;
class CustomerController extends Controller
{
    public function __construct(
        private CustomerService $customerService
    ) {
    }
    public function index(): JsonResponse
    {
        return response()->json(
            $this->customerService->getAll()
        );
    }
    public function show(
        int $customer
    ): JsonResponse {
        return response()->json(
            $this->customerService
                ->findById($customer)
        );
    }
    public function store(
        StoreCustomerRequest $request
    ): JsonResponse {
        $customer =
            $this->customerService->create(
                $request->validated()
            );
        return response()->json(
            $customer,
            201
        );
    }
    public function update(
        UpdateCustomerRequest $request,
        int $customer
    ): JsonResponse {
        $customer =
            $this->customerService->update(
                $customer,
                $request->validated()
            );
        return response()->json($customer);
    }
    public function destroy(
        int $customer
    ): JsonResponse {
        $this->customerService
            ->delete($customer);
        return response()->json([
            'message' =>
                'Customer deleted successfully.',
        ]);
    }
}