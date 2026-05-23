<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Models\Loan;

#[Signature('loans:scan-late')]
#[Description('Actualiza el estado de los préstamos basado en el calendario de cuotas vencidas.')]
class ScanLateLoans extends Command
{
    public function handle()
    {
        // 1. Escaneo de Mora para Préstamos por Cuotas
        $installmentLoans = \App\Models\Loan::where('type', 'installments')
            ->whereIn('status', ['active', 'late'])
            ->get();

        foreach ($installmentLoans as $loan) {
            $isLate = false;
            foreach ($loan->getAmortizationSchedule() as $installment) {
                if ($installment['status'] === 'late') {
                    $isLate = true;
                    break;
                }
            }

            if ($isLate && $loan->status !== 'late') {
                $loan->update(['status' => 'late']);
                $this->info("Préstamo #{$loan->id} marcado como ATRASADO.");
                \App\Models\ActivityLog::log("Préstamo marcado automáticamente como EN MORA por cuotas vencidas", $loan);
            } elseif (!$isLate && $loan->status === 'late') {
                $loan->update(['status' => 'active']);
                $this->info("Préstamo #{$loan->id} vuelto a estado ACTIVO.");
            }
        }

        // 2. Capitalización de Intereses para Préstamos de Pagos Varios ("Open")
        $openLoans = \App\Models\Loan::where('type', 'open')
            ->whereIn('status', ['active', 'late'])
            ->get();

        foreach ($openLoans as $loan) {
            $lastInterest = $loan->last_interest_at ?? $loan->start_date;
            $nextInterestDate = (clone $lastInterest)->addMonth();

            while (now()->greaterThanOrEqualTo($nextInterestDate)) {
                $interestAmount = $loan->balance * ($loan->interest_rate / 100);
                $oldBalance = $loan->balance;
                $loan->balance += $interestAmount;
                $loan->last_interest_at = $nextInterestDate;
                $loan->save();

                $this->info("Interés capitalizado para Préstamo #{$loan->id}: ${$interestAmount}");
                
                \App\Models\ActivityLog::log(
                    "Capitalización de interés mensual ({$loan->interest_rate}%). Saldo anterior: $" . number_format((float)$oldBalance, 2) . " -> Nuevo saldo: $" . number_format((float)$loan->balance, 2),
                    $loan
                );

                $nextInterestDate = (clone $nextInterestDate)->addMonth();
            }
        }

        $this->info('Escaneo y capitalización completados.');
    }
}
