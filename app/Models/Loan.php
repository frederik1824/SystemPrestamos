<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Loan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_id',
        'amount',
        'interest_rate',
        'interest_type',
        'payment_modality',
        'installments',
        'installment_amount',
        'start_date',
        'estimated_end_date',
        'late_fee_percentage',
        'balance',
        'status',
        'type',
        'last_interest_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'estimated_end_date' => 'date',
        'last_interest_at' => 'datetime',
        'amount' => 'decimal:2',
        'balance' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'late_fee_percentage' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function collaterals()
    {
        return $this->hasMany(Collateral::class);
    }

    public function calculateInterests()
    {
        if ($this->type === 'open') {
            // Para préstamos open, esto solo retorna el interés inicial del primer mes
            return (float) $this->amount * ((float) $this->interest_rate / 100);
        }

        return self::calculateTotalInterest(
            (float) $this->amount, 
            (float) $this->interest_rate, 
            $this->installments, 
            $this->interest_type
        );
    }

    public static function calculateTotalInterest($amount, $rate, $installments, $type = 'simple')
    {
        if ($type === 'simple') {
            return $amount * ($rate / 100);
        }

        // Interés amortizado (Francés) - Estimación simplificada del interés total
        $pmt = self::calculateInstallment($amount, $rate, $installments, 'compound');
        return ($pmt * $installments) - $amount;
    }

    public static function calculateInstallment($amount, $rate, $installments, $type = 'simple')
    {
        $rate = (float) $rate / 100;
        
        if ($type === 'simple') {
            // (Capital + Interés Total) / Cuotas
            $total = (float) $amount + ((float) $amount * $rate);
            return $total / max(1, $installments);
        }

        // Francés: P * (i * (1 + i)^n) / ((1 + i)^n - 1)
        // Asumiendo que la tasa es por cuota
        if ($rate <= 0) return (float) $amount / max(1, $installments);
        
        $factor = pow(1 + $rate, $installments);
        return ((float) $amount * ($rate * $factor)) / ($factor - 1);
    }

    public function calculateLateFees()
    {
        if ($this->type !== 'installments') return 0;
        
        $schedule = $this->getAmortizationSchedule();
        $totalLateFees = 0;
        $moraRate = (float) $this->late_fee_percentage / 100;

        foreach ($schedule as $inst) {
            if ($inst['status'] === 'late') {
                $totalLateFees += (float) $inst['amount'] * $moraRate;
            }
        }

        return $totalLateFees;
    }

    public function updateBalance()
    {
        if ($this->type === 'installments') {
            $totalPaid = (float) $this->payments()->sum('amount');
            $baseDebt = (float) $this->amount + (float) $this->calculateInterests();
            $lateFees = (float) $this->calculateLateFees();
            $this->balance = $baseDebt + $lateFees - $totalPaid;
        } else {
            // Para 'open' loans, el balance no se puede recalcular desde el monto inicial
            // porque los intereses se van acumulando mes a mes sobre el saldo pendiente.
            // Solo nos aseguramos de que el estado sea correcto.
        }
        
        if ($this->balance <= 0) {
            $this->status = 'paid';
            $this->balance = 0;
        } elseif ($this->type === 'installments' && $this->estimated_end_date && $this->estimated_end_date->isPast() && $this->balance > 0) {
            $this->status = 'late';
        }
        
        $this->save();
    }

    public function getAmortizationSchedule()
    {
        $schedule = [];
        $startDate = \Carbon\Carbon::parse($this->start_date);
        $totalPaid = (float) $this->payments()->sum('amount');
        $amountPerInstallment = (float) $this->installment_amount;
        
        // Determinar cuántas cuotas se han pagado proporcionalmente
        $paidInstallments = floor($totalPaid / max(0.01, $amountPerInstallment));

        for ($i = 1; $i <= $this->installments; $i++) {
            $dueDate = match (strtolower($this->payment_modality)) {
                'diario', 'daily' => $startDate->copy()->addDays($i),
                'semanal', 'weekly' => $startDate->copy()->addWeeks($i),
                'quincenal', 'biweekly' => $startDate->copy()->addDays($i * 15),
                'mensual', 'monthly' => $startDate->copy()->addMonths($i),
                default => $startDate->copy()->addMonths($i),
            };

            $status = 'pending';
            if ($i <= $paidInstallments) {
                $status = 'paid';
            } elseif ($dueDate < now()->startOfDay()) {
                $status = 'late';
            }

            $moraAmount = 0;
            if ($status === 'late' && (float)$this->late_fee_percentage > 0) {
                $moraAmount = (float)$amountPerInstallment * ((float)$this->late_fee_percentage / 100);
            }

            $schedule[] = [
                'number' => $i,
                'due_date' => $dueDate,
                'amount' => $amountPerInstallment,
                'mora' => $moraAmount,
                'total' => $amountPerInstallment + $moraAmount,
                'status' => $status
            ];
        }

        return $schedule;
    }

    public function getRepaymentProgress()
    {
        $totalToRecover = (float) ($this->amount + $this->calculateInterests());
        if ($totalToRecover <= 0) return 0;
        
        $totalPaid = (float) $this->payments()->sum('amount');
        return min(100, round(($totalPaid / $totalToRecover) * 100));
    }

    public function getNextDueDate()
    {
        $schedule = $this->getAmortizationSchedule();
        foreach ($schedule as $inst) {
            if ($inst['status'] !== 'paid') {
                return $inst['due_date'];
            }
        }
        return null;
    }
}
