<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with('loan.customer');

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->whereHas('loan.customer', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $payments = $query->latest()->paginate(10);
        return view('payments.index', compact('payments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'loan_id' => 'required|exists:loans,id',
            'type' => 'nullable|in:regular,capital',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string',
            'observations' => 'nullable|string',
        ]);

        $loan = Loan::findOrFail($validated['loan_id']);

        if ($loan->status === 'paid') {
            return back()->with('error', 'No se pueden realizar más pagos a un préstamo que ya ha sido saldado.');
        }

        $isCapital = ($validated['type'] ?? 'regular') === 'capital';

        if ($isCapital && $validated['amount'] > $loan->getEffectiveAmount()) {
            return back()->withErrors(['amount' => 'El abono a capital no puede ser mayor al capital pendiente ($' . number_format((float) $loan->getEffectiveAmount(), 2) . ').']);
        } elseif (!$isCapital && $validated['amount'] > $loan->balance) {
            return back()->withErrors(['amount' => 'El monto del pago no puede ser mayor al balance pendiente ($' . number_format((float) $loan->balance, 2) . ').']);
        }

        $payment = Payment::create($validated);
        
        $logMsg = $isCapital 
            ? "Abono a capital registrado por $" . number_format($payment->amount, 2)
            : "Cobro de cuota(s) registrado por $" . number_format($payment->amount, 2);

        \App\Models\ActivityLog::log($logMsg, $loan, [
            'method' => $payment->payment_method,
            'date' => $payment->payment_date
        ]);

        return back()->with('success', 'Pago registrado correctamente.');
    }

    public function receipt(Payment $payment)
    {
        $payment->load('loan.customer');
        return view('payments.receipt', compact('payment'));
    }
}
