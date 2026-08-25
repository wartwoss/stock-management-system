<?php
namespace App\Domain\Appliance\Services;
use App\Domain\Appliance\Models\Appliance;
use Illuminate\Database\Eloquent\Collection;
class ApplianceService
{
    public function getAll(): Collection
    {
        return Appliance::query()
            ->orderByDesc('id')
            ->get();
    }
    public function findById(int $id): Appliance
    {
        return Appliance::findOrFail($id);
    }
    public function create(array $data): Appliance
    {
        return Appliance::create($data);
    }
    public function update(
        int $id,
        array $data
    ): Appliance {
        $appliance = $this->findById($id);
        $appliance->update($data);
        return $appliance->refresh();
    }
    public function delete(int $id): void
    {
        $appliance = $this->findById($id);
        $appliance->delete();
    }
}