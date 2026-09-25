<?php
namespace App\Domain\Credit\Services;
use App\Domain\Credit\Models\Credit;
use App\Domain\Sale\Models\Sale;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
class CreditService
{
    public function getAll(): Collection
    {
        return Credit::with([
            'sale.appliance' => fn($q) => $q->withTrashed(),
            'customer',
        ])
            ->orderByDesc('id')
            ->get();
    }
    public function findById(int $id): Credit
    {
        return Credit::with([
            'sale.appliance' => fn($q) => $q->withTrashed(),
            'customer',
        ])->findOrFail($id);
    }
    public function create(array $data): Credit
    {
        return DB::transaction(function () use ($data) {
            $sale = Sale::findOrFail(
                $data['sale_id']
            );
            if ($sale->payment_type !== 'credit') {
                throw ValidationException::withMessages([
                    'sale_id' =>
                        'Only credit sales can have a credit agreement.',
                ]);
            }
            if (!$sale->customer_id) {
                throw ValidationException::withMessages([
                    'sale_id' =>
                        'The credit sale must have a customer.',
                ]);
            }
            $totalAmount = (float) $sale->total_price;
            $downPayment =
                (float) $data['down_payment'];
            if ($downPayment >= $totalAmount) {
                throw ValidationException::withMessages([
                    'down_payment' =>
                        'Down payment must be less than the total amount.',
                ]);
            }
            if (
                $data['first_due_date']
                < $sale->sale_date->format('Y-m-d')
            ) {
                throw ValidationException::withMessages([
                    'first_due_date' =>
                        'First due date cannot be before the sale date.',
                ]);
            }
            $remainingDebt =
                $totalAmount - $downPayment;
            $installmentAmount = round(
                $remainingDebt
                / $data['number_of_payments'],
                2
            );
            $credit = Credit::create([
                'sale_id' => $sale->id,
                'customer_id' =>
                    $sale->customer_id,
                'total_amount' =>
                    $totalAmount,
                'down_payment' =>
                    $downPayment,
                'remaining_debt' =>
                    $remainingDebt,
                'installment_amount' =>
                    $installmentAmount,
                'number_of_payments' =>
                    $data['number_of_payments'],
                'payments_made' => 0,
                'first_due_date' =>
                    $data['first_due_date'],
                'next_due_date' =>
                    $data['first_due_date'],
                'status' => 'active',
                'currency' => $sale->currency,
                'exchange_rate_per_100' => $sale->exchange_rate_per_100,
            ]);
            return $credit->load([
                'sale.appliance' => fn($q) => $q->withTrashed(),
                'customer',
            ]);
        });
    }
    public function update(
        int $id,
        array $data
    ): Credit {
        return DB::transaction(
            function () use ($id, $data) {
                $credit = Credit::findOrFail($id);
                if (
                    isset($data['number_of_payments'])
                    && $data['number_of_payments']
                    < $credit->payments_made
                ) {
                    throw ValidationException::withMessages([
                        'number_of_payments' =>
                            'Number of payments cannot be less than payments already made.',
                    ]);
                }
                $credit->fill($data);
                if (isset($data['number_of_payments'])) {
                    $remainingPayments =
                        $data['number_of_payments']
                        - $credit->payments_made;
                    if ($remainingPayments <= 0) {
                        throw ValidationException::withMessages([
                            'number_of_payments' =>
                                'There must be at least one remaining installment.',
                        ]);
                    }
                    $credit->installment_amount =
                        round(
                            (float) $credit->remaining_debt
                            / $remainingPayments,
                            2
                        );
                }
                if (
                    isset($data['first_due_date'])
                    && $credit->payments_made === 0
                    && !isset($data['next_due_date'])
                ) {
                    $credit->next_due_date =
                        $data['first_due_date'];
                }
                $credit->save();
                return $credit
                    ->refresh()
                    ->load([
                        'sale.appliance' => fn($q) => $q->withTrashed(),
                        'customer',
                    ]);
            }
        );
    }
    public function getUpcoming(
        int $days = 7
    ): Collection {
        $today = now()->toDateString();
        $until = now()
            ->addDays($days)
            ->toDateString();
        return Credit::with('customer')
            ->where(
                'remaining_debt',
                '>',
                0
            )
            ->whereBetween(
                'next_due_date',
                [$today, $until]
            )
            ->orderBy('next_due_date')
            ->get();
    }
    public function getOverdue(): Collection
    {
        return Credit::with('customer')
            ->where(
                'remaining_debt',
                '>',
                0
            )
            ->whereDate(
                'next_due_date',
                '<',
                now()->toDateString()
            )
            ->orderBy('next_due_date')
            ->get();
    }
    public function getCompleted(): Collection
    {
        return Credit::with('customer')
            ->where(
                'remaining_debt',
                '<=',
                0
            )
            ->get();
    }
    public function getSummary(
        int $id
    ): Credit {
        return Credit::with([
            'sale.appliance' => fn($q) => $q->withTrashed(),
            'customer',
        ])->findOrFail($id);
    }
}
