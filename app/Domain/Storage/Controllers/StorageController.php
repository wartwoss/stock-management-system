<?php
namespace App\Domain\Storage\Controllers;
use App\Http\Controllers\Controller;
use App\Domain\Storage\Requests\StoreStorageRequest;
use App\Domain\Storage\Requests\UpdateStorageRequest;
use App\Domain\Storage\Services\StorageService;
use Illuminate\Http\JsonResponse;
class StorageController extends Controller
{
    public function __construct(
        private StorageService $storageService
    ) {
    }
    public function index(): JsonResponse
    {
        return response()->json(
            $this->storageService->getAll()
        );
    }
    public function show(int $storage): JsonResponse
    {
        return response()->json(
            $this->storageService->findById($storage)
        );
    }
    public function store(
        StoreStorageRequest $request
    ): JsonResponse {
        $storage = $this->storageService->create(
            $request->validated()
        );
        return response()->json($storage, 201);
    }
    public function update(
        UpdateStorageRequest $request,
        int $storage
    ): JsonResponse {
        $storage = $this->storageService->update(
            $storage,
            $request->validated()
        );
        return response()->json($storage);
    }
    public function destroy(int $storage): JsonResponse
    {
        $this->storageService->delete($storage);
        return response()->json([
            'message' => 'Storage deleted successfully.',
        ]);
    }
}