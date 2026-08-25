<?php
namespace App\Domain\Customer\Services;
use App\Domain\Customer\Models\Customer;
use Illuminate\Database\Eloquent\Collection;
class CustomerService
{
    public function getAll(): Collection
    {
        return Customer::query()
            ->orderByDesc('id')
            ->get();
    }
    public function findById(int $id): Customer
    {
        return Customer::findOrFail($id);
    }
    public function create(array $data): Customer
    {
        return Customer::create($data);
    }
    public function update(
        int $id,
        array $data
    ): Customer {
        $customer = $this->findById($id);
        $customer->update($data);
        return $customer->refresh();
    }
    public function delete(int $id): void
    {
        $customer = $this->findById($id);
        $customer->delete();
    }
    public function findByPhone(
        string $phoneNumber
    ): ?Customer {
        return Customer::query()
            ->where(
                'phone_number',
                $phoneNumber
            )
            ->first();
    }
}
