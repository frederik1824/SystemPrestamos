<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Customer;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    public function index(Request $request)
    {
        $query = Loan::with('customer');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('customer', function($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%")
                       ->orWhere('identification_id', 'like', "%{$search}%");
                })->orWhere('id', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $loans = $query->latest()->paginate(10)->withQueryString();
        
        // Stats for the header
        $stats = [
            'total_active' => Loan::where('status', 'active')->count(),
            'total_late' => Loan::where('status', 'late')->count(),
            'total_capital' => Loan::where('status', 'active')->sum('balance'),
        ];

        return view('loans.index', compact('loans', 'stats'));
    }

    public function create(Request $request)
    {
        $customers = Customer::all();
        $selectedCustomerId = $request->get('customer_id');

        return view('loans.create', compact('customers', 'selectedCustomerId'));
    }

    public function store(Request $request)
    {
        $type = $request->get('type', 'installments');

        if ($type === 'open') {
            $validated = $request->validate([
                'customer_id' => 'required|exists:customers,id',
                'amount' => 'required|numeric|min:1',
                'interest_rate' => 'required|numeric|min:0',
                'late_fee_percentage' => 'nullable|numeric|min:0',
                'start_date' => 'required|date',
                'type' => 'required|in:installments,open',
            ]);
            $validated['interest_type'] = 'simple';
            $validated['payment_modality'] = 'mensual';
            $validated['installments'] = 0;
            $validated['installment_amount'] = 0;
        } else {
            $validated = $request->validate([
                'customer_id' => 'required|exists:customers,id',
                'amount' => 'required|numeric|min:1',
                'interest_rate' => 'required|numeric|min:0',
                'late_fee_percentage' => 'nullable|numeric|min:0',
                'interest_type' => 'required|in:simple,compound',
                'payment_modality' => 'required|in:semanal,quincenal,mensual',
                'installments' => 'required|integer|min:1',
                'start_date' => 'required|date',
                'type' => 'required|in:installments,open',
            ]);
        }

        $loan = new Loan($validated);
        $startDate = \Carbon\Carbon::parse($validated['start_date']);

        if ($loan->type === 'open') {
            $loan->last_interest_at = $startDate;
            $loan->estimated_end_date = null;
            $loan->balance = $loan->amount + ($loan->amount * ($loan->interest_rate / 100));
        } else {
            $days = match($validated['payment_modality']) {
                'semanal' => 7,
                'quincenal' => 15,
                'mensual' => 30,
                default => 30,
            };
            $loan->estimated_end_date = (clone $startDate)->addDays($days * $validated['installments']);
            
            $loan->installment_amount = Loan::calculateInstallment(
                $validated['amount'], 
                $validated['interest_rate'], 
                $validated['installments'], 
                $validated['interest_type']
            );

            $loan->balance = $loan->amount + $loan->calculateInterests();
        }

        $loan->save();

        \App\Models\ActivityLog::log(
            "Nuevo préstamo (" . ($loan->type === 'open' ? 'Pagos Varios' : 'Cuotas') . ") registrado para {$loan->customer->name} por $" . number_format($loan->amount, 2), 
            $loan
        );

        return redirect()->route('loans.show', $loan)->with('success', 'Préstamo creado correctamente.');
    }

    public function edit(Loan $loan)
    {
        if ($loan->payments()->count() > 0 || $loan->status === 'paid') {
            return redirect()->route('loans.show', $loan)->with('error', 'No se puede editar un préstamo que ya tiene pagos o está saldado por razones de seguridad contable.');
        }

        $customers = Customer::all();
        return view('loans.edit', compact('loan', 'customers'));
    }

    public function update(Request $request, Loan $loan)
    {
        if ($loan->payments()->count() > 0 || $loan->status === 'paid') {
            return redirect()->route('loans.show', $loan)->with('error', 'Acción bloqueada: El préstamo es inmutable debido a su historial de pagos.');
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'interest_rate' => 'required|numeric|min:0',
            'late_fee_percentage' => 'nullable|numeric|min:0',
            'interest_type' => 'required|in:simple,compound',
            'payment_modality' => 'required|in:libre,semanal,quincenal,mensual',
            'installments' => 'required|integer|min:1',
            'start_date' => 'required|date',
        ]);

        $loan->update($validated);
        
        \App\Models\ActivityLog::log("Términos del préstamo #{$loan->id} actualizados", $loan, $validated);

        return redirect()->route('loans.show', $loan)->with('success', 'Préstamo actualizado.');
    }

    public function show(Loan $loan)
    {
        $loan->load('customer', 'payments', 'collaterals');
        return view('loans.show', compact('loan'));
    }

    public function settle(Request $request, Loan $loan)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
        ]);

        // Registrar el pago de liquidación
        $loan->payments()->create([
            'amount' => $validated['amount'],
            'payment_date' => now(),
            'payment_method' => 'Liquidación Especial',
            'observations' => 'Saldado por acuerdo especial.',
        ]);

        // Forzar balance a 0 y estado a pagado
        $loan->update([
            'balance' => 0,
            'status' => 'paid',
        ]);

        \App\Models\ActivityLog::log("Liquidación especial procesada por $" . number_format($validated['amount'], 2), $loan);

        return redirect()->route('loans.show', $loan)->with('success', 'Préstamo liquidado correctamente.');
    }

    public function reports()
    {
        $loans = Loan::with('customer')->latest()->get();
        return view('reports.index', compact('loans'));
    }

    public function document(Loan $loan)
    {
        $loan->load('customer');
        return view('loans.document', compact('loan'));
    }
}
