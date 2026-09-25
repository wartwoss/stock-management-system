<?php
namespace App\Domain\Payment\Services;
use App\Domain\Payment\Models\Payment;
use App\Domain\Credit\Models\Credit;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
class PaymentService
{
    public function getAll(): Collection
    {
        return Payment::with([
            'credit.customer',
            'credit.sale.appliance' => fn($q) => $q->withTrashed(),
        ])
            ->orderByDesc('id')
            ->get();
    }
    public function findById(int $id): Payment
    {
        return Payment::with([
            'credit.customer',
            'credit.sale.appliance' => fn($q) => $q->withTrashed(),
        ])->findOrFail($id);
    }
    public function getByCredit(
        int $creditId
    ): Collection {
        Credit::findOrFail($creditId);
        return Payment::query()
            ->where('credit_id', $creditId)
            ->orderByDesc('payment_date')
            ->get();
    }
    public function recordPayment(
        int $creditId,
        array $data
    ): Payment {
        return DB::transaction(
            function () use ($creditId, $data) {
                $credit = Credit::query()
                    ->lockForUpdate()
                    ->findOrFail($creditId);
                if (
                    (float) $credit->remaining_debt <= 0
                    || $credit->status === 'completed'
                ) {
                    throw ValidationException::withMessages([
                        'credit' =>
                            'This credit has already been completely paid.',
                    ]);
                }
                $amount = round(
                    (float) $data['amount'],
                    2
                );
                $remainingDebt =
                    (float) $credit->remaining_debt;
                if ($amount > $remainingDebt) {
                    throw ValidationException::withMessages([
                        'amount' =>
                            'Payment cannot be greater than the remaining debt.',
                    ]);
                }
                $payment = Payment::create([
                    'credit_id' => $credit->id,
                    'amount' => $amount,
                    'payment_date' =>
                        $data['payment_date'],
                    'currency' => $credit->currency,
                    'exchange_rate_per_100' => $credit->exchange_rate_per_100,
                ]);
                $credit->remaining_debt = round(
                    $remainingDebt - $amount,
                    2
                );
                $credit->payments_made++;
                if (
                    (float) $credit->remaining_debt <= 0
                ) {
                    $credit->remaining_debt = 0;
                    $credit->status = 'completed';
                    $credit->next_due_date = null;
                } else {
                    if ($credit->next_due_date) {
                        $nextDueDate = Carbon::parse(
                            $credit->next_due_date
                        )->addMonthNoOverflow();
                        $credit->next_due_date =
                            $nextDueDate->toDateString();
                        $credit->status =
                            $nextDueDate->isBefore(
                                today()
                            )
                                ? 'overdue'
                                : 'active';
                    }
                }
                $credit->save();
                return $payment
                    ->refresh()
                    ->load([
                        'credit.customer',
                        'credit.sale.appliance' => fn($q) => $q->withTrashed(),
                    ]);
            }
        );
    }
}
