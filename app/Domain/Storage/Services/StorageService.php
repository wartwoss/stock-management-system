<?php
namespace App\Domain\Storage\Services;
use App\Domain\Storage\Models\Storage;
use Illuminate\Database\Eloquent\Collection;
class StorageService
{
    public function getAll(): Collection
    {
        return Storage::query()
            ->orderByDesc('id')
            ->get();
    }
    public function findById(int $id): Storage
    {
        return Storage::findOrFail($id);
    }
    public function create(array $data): Storage
    {
        return Storage::create($data);
    }
    public function update(
        int $id,
        array $data
    ): Storage {
        $storage = $this->findById($id);
        $storage->update($data);
        return $storage->refresh();
    }
    public function delete(int $id): void
    {
        $storage = $this->findById($id);
        $storage->delete();
    }
}