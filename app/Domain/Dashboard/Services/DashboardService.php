<?php
namespace App\Domain\Dashboard\Services;
use App\Domain\Inventory\Models\Inventory;
use App\Domain\Storage\Models\Storage;
use App\Domain\Sale\Models\Sale;
use App\Domain\Credit\Models\Credit;
use App\Domain\Payment\Models\Payment;
class DashboardService
{
    public function getSummary(
        array $filters = []
    ): array {
        return [
            'inventory' =>
                $this->getInventorySummary($filters),
            'sales' =>
                $this->getSalesSummary($filters),
            'credits' =>
                $this->getCreditSummary(),
        ];
    }
    public function getInventorySummary(
        array $filters = []
    ): array {
        $threshold =
            $filters['low_stock_threshold'] ?? 3;
        $totalInStock = Inventory::sum(
            'quantity_in_stock'
        );
        $totalSold = Inventory::sum(
            'quantity_sold'
        );
        $lowStock = Inventory::with([
            'appliance',
            'storage',
        ])
            ->where(
                'quantity_in_stock',
                '>',
                0
            )
            ->where(
                'quantity_in_stock',
                '<=',
                $threshold
            )
            ->get();
        $outOfStock = Inventory::with([
            'appliance',
            'storage',
        ])
            ->where(
                'quantity_in_stock',
                0
            )
            ->get();
        $byStorage = Storage::with([
            'inventories.appliance',
        ])
            ->get()
            ->map(function ($storage) {
                return [
                    'storage_id' =>
                        $storage->id,
                    'storage_name' =>
                        $storage->name,
                    'location' =>
                        $storage->location,
                    'total_quantity' =>
                        $storage->inventories
                            ->sum(
                                'quantity_in_stock'
                            ),
                    'appliances' =>
                        $storage->inventories
                            ->map(function ($inventory) {
                                return [
                                    'appliance_id' =>
                                        $inventory
                                            ->appliance_id,
                                    'name' =>
                                        $inventory
                                            ->appliance
                                            ?->name,
                                    'quantity_in_stock' =>
                                        $inventory
                                            ->quantity_in_stock,
                                ];
                            })
                            ->values(),
                ];
            })
            ->values();
        return [
            'total_in_stock' =>
                (int) $totalInStock,
            'total_sold' =>
                (int) $totalSold,
            'low_stock_threshold' =>
                (int) $threshold,
            'low_stock_count' =>
                $lowStock->count(),
            'low_stock' =>
                $lowStock,
            'out_of_stock_count' =>
                $outOfStock->count(),
            'out_of_stock' =>
                $outOfStock,
            'by_storage' =>
                $byStorage,
        ];
    }
    public function getSalesSummary(
        array $filters = []
    ): array {
        $query = Sale::query();
        if (!empty($filters['date_from'])) {
            $query->whereDate(
                'sale_date',
                '>=',
                $filters['date_from']
            );
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate(
                'sale_date',
                '<=',
                $filters['date_to']
            );
        }
        $sales = $query->get();
        return [
            'total_sales' =>
                $sales->count(),
            'total_units_sold' =>
                (int) $sales->sum('quantity'),
            'total_sales_amount' =>
                round(
                    (float) $sales->sum(
                        'total_price'
                    ),
                    2
                ),
            'cash_sales' =>
                $sales
                    ->where(
                        'payment_type',
                        'cash'
                    )
                    ->count(),
            'credit_sales' =>
                $sales
                    ->where(
                        'payment_type',
                        'credit'
                    )
                    ->count(),
            'date_from' =>
                $filters['date_from'] ?? null,
            'date_to' =>
                $filters['date_to'] ?? null,
        ];
    }
    public function getCreditSummary(): array
    {
        $outstandingDebt = Credit::query()
            ->where(
                'remaining_debt',
                '>',
                0
            )
            ->sum('remaining_debt');
        $monthlyPayments = Payment::query()
            ->whereYear(
                'payment_date',
                now()->year
            )
            ->whereMonth(
                'payment_date',
                now()->month
            )
            ->sum('amount');
        $overdueCredits = Credit::with([
            'customer',
            'sale.appliance',
        ])
            ->where(
                'remaining_debt',
                '>',
                0
            )
            ->whereNotNull(
                'next_due_date'
            )
            ->whereDate(
                'next_due_date',
                '<',
                today()
            )
            ->get();
        $overdueCustomers =
            $overdueCredits
                ->groupBy('customer_id')
                ->map(function ($credits) {
                    $first =
                        $credits->first();
                    return [
                        'customer_id' =>
                            $first->customer_id,
                        'customer_name' =>
                            $first->customer
                                ?->name,
                        'phone_number' =>
                            $first->customer
                                ?->phone_number,
                        'outstanding_debt' =>
                            round(
                                (float) $credits
                                    ->sum(
                                        'remaining_debt'
                                    ),
                                2
                            ),
                        'overdue_credits' =>
                            $credits->count(),
                    ];
                })
                ->values();
        return [
            'total_outstanding_debt' =>
                round(
                    (float) $outstandingDebt,
                    2
                ),
            'monthly_payments_received' =>
                round(
                    (float) $monthlyPayments,
                    2
                ),
            'payment_month' =>
                now()->format('Y-m'),
            'overdue_customers_count' =>
                $overdueCustomers->count(),
            'overdue_customers' =>
                $overdueCustomers,
            'completed_credits' =>
                Credit::query()
                    ->where(
                        'status',
                        'completed'
                    )
                    ->count(),
            'active_credits' =>
                Credit::query()
                    ->where(
                        'remaining_debt',
                        '>',
                        0
                    )
                    ->count(),
        ];
    }
}