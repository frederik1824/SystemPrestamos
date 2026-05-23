<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalLent = Loan::sum('amount');
        $totalRecovered = \App\Models\Payment::sum('amount');
        $totalLate = Loan::where('status', 'late')->sum('balance');
        $activeLoansCount = Loan::where('status', 'active')->count();
        
        // Calcular Beneficios Reales (Intereses Cobrados)
        // Beneficio = (Interés_Total / Monto_Total_a_Recuperar) * Pagos_Recibidos
        $loans = Loan::with('payments')->get();
        $totalEarnings = 0;
        foreach ($loans as $loan) {
            $totalToRecover = (float) $loan->amount + (float) $loan->calculateInterests();
            if ($totalToRecover > 0) {
                $totalPaid = (float) $loan->payments->sum('amount');
                $interestRatio = (float) $loan->calculateInterests() / $totalToRecover;
                $totalEarnings += $totalPaid * $interestRatio;
            }
        }

        $lastPayments = \App\Models\Payment::with('loan.customer')->latest()->take(5)->get();

        // Datos para gráfico (últimos 6 meses)
        $paymentsLast6Months = \App\Models\Payment::with('loan')
            ->where('payment_date', '>=', now()->subMonths(6)->startOfMonth())
            ->orderBy('payment_date', 'asc')
            ->get();

        $monthlyStats = $paymentsLast6Months->groupBy(function($payment) {
            return $payment->payment_date->format('m/Y');
        })->map(function($monthPayments) {
            $total = 0;
            $profit = 0;
            foreach ($monthPayments as $monthPayment) {
                $total += (float)$monthPayment->amount;
                $loan = $monthPayment->loan;
                $totalToRecover = (float)$loan->amount + (float)$loan->calculateInterests();
                if ($totalToRecover > 0) {
                    $ratio = (float)$loan->calculateInterests() / $totalToRecover;
                    $profit += (float)$monthPayment->amount * $ratio;
                }
            }
            return ['total' => $total, 'profit' => $profit];
        });

        $chartLabels = $monthlyStats->keys();
        $chartData = $monthlyStats->pluck('total');
        $chartProfitData = $monthlyStats->pluck('profit');

        // Alertas de Cuotas
        $alerts = [];
        $activeLoans = Loan::where('status', 'active')->with('customer')->get();
        foreach ($activeLoans as $loan) {
            $schedule = $loan->getAmortizationSchedule();
            foreach ($schedule as $inst) {
                if ($inst['status'] === 'late') {
                    $alerts[] = [
                        'type' => 'late',
                        'customer' => $loan->customer->name,
                        'loan_id' => $loan->id,
                        'due_date' => $inst['due_date'],
                        'amount' => $inst['amount'],
                        'installment_no' => $inst['number']
                    ];
                } elseif ($inst['status'] === 'pending' && $inst['due_date']->isToday()) {
                    $alerts[] = [
                        'type' => 'due_today',
                        'customer' => $loan->customer->name,
                        'loan_id' => $loan->id,
                        'due_date' => $inst['due_date'],
                        'amount' => $inst['amount'],
                        'installment_no' => $inst['number']
                    ];
                }
            }
        }
        
        // Ordenar alertas: primero las vencidas, luego las de hoy
        usort($alerts, function($a, $b) {
            return $a['due_date']->timestamp <=> $b['due_date']->timestamp;
        });

        return view('dashboard', compact(
            'totalLent',
            'totalRecovered',
            'totalEarnings',
            'totalLate',
            'activeLoansCount',
            'lastPayments',
            'chartLabels',
            'chartData',
            'chartProfitData',
            'alerts'
        ));
    }
}
