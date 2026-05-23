<x-layouts.app>
    <div id="receipt-content" class="max-w-md mx-auto my-10 bg-white p-6 border border-slate-200 shadow-sm print:shadow-none print:border-none print:my-0 print:p-2 text-black">
        
        <!-- Header -->
        <div class="text-center mb-4 border-b-2 border-black border-dashed pb-4">
            <h1 class="text-lg font-black uppercase tracking-tighter">Detalle de Préstamo</h1>
            <p class="text-[10px] font-bold uppercase tracking-widest mt-1">SysPrestamos</p>
            <p class="text-[10px] font-bold mt-2">NÚMERO: #CON-{{ str_pad($loan->id, 6, '0', STR_PAD_LEFT) }}</p>
            <p class="text-[10px] font-bold">FECHA: {{ \Carbon\Carbon::parse($loan->start_date)->format('d/m/Y') }}</p>
        </div>

        <!-- Customer Info -->
        <div class="mb-4 border-b border-black/20 pb-2">
            <h2 class="text-[10px] font-black uppercase tracking-widest text-center mb-2">Información del Cliente</h2>
            <div class="space-y-1">
                <span class="text-[10px] font-black uppercase tracking-widest block">Nombre Completo</span>
                <span class="text-xs font-black uppercase block">{{ $loan->customer->name }}</span>
            </div>
            <div class="space-y-1 mt-2">
                <span class="text-[10px] font-black uppercase tracking-widest block">Identificación</span>
                <span class="text-xs font-bold uppercase block">{{ $loan->customer->identification }}</span>
            </div>
        </div>

        <!-- Financial Terms -->
        <div class="mb-4 border-b border-black/20 pb-2">
            <h2 class="text-[10px] font-black uppercase tracking-widest text-center mb-2">Condiciones</h2>
            <div class="flex justify-between items-end border-b border-black/10 pb-1 mb-1">
                <span class="text-[10px] font-black uppercase">Capital</span>
                <span class="text-sm font-black">${{ number_format((float) $loan->amount, 2) }}</span>
            </div>
            <div class="flex justify-between items-end border-b border-black/10 pb-1 mb-1">
                <span class="text-[10px] font-black uppercase">Tasa (%)</span>
                <span class="text-sm font-bold">{{ $loan->interest_rate }}%</span>
            </div>
            <div class="flex justify-between items-end border-b border-black/10 pb-1 mb-1">
                <span class="text-[10px] font-black uppercase">Modalidad</span>
                <span class="text-xs font-bold uppercase">{{ $loan->payment_modality }}</span>
            </div>
            <div class="flex justify-between items-end mt-2">
                <span class="text-[10px] font-black uppercase">Total Pagar</span>
                <span class="text-sm font-black">${{ number_format($loan->amount + $loan->calculateInterests(), 2) }}</span>
            </div>
        </div>

        <!-- Amortization / Details -->
        <div class="mb-8">
            @if($loan->type === 'installments')
            <h2 class="text-[10px] font-black uppercase tracking-widest text-center mb-2">Cronograma de Cuotas</h2>
            <table class="w-full text-left">
                <thead>
                    <tr class="uppercase text-[9px] font-black border-b border-black">
                        <th class="py-1">No.</th>
                        <th class="py-1">Vence</th>
                        <th class="py-1 text-right">Monto</th>
                    </tr>
                </thead>
                <tbody class="text-[10px]">
                    @foreach($loan->getAmortizationSchedule() as $installment)
                    <tr class="border-b border-black/10">
                        <td class="py-1 font-black">#{{ $installment['number'] }}</td>
                        <td class="py-1 font-bold">{{ $installment['due_date']->format('d/m/y') }}</td>
                        <td class="py-1 font-black text-right">${{ number_format((float) $installment['amount'], 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <h2 class="text-[10px] font-black uppercase tracking-widest text-center mb-2">Términos Pagos Varios</h2>
            <div class="text-[9px] font-medium leading-relaxed">
                <p class="mb-2">Préstamo sin cuotas fijas. El cliente abonará según capacidad.</p>
                <p class="mb-1">- Interés del <strong>{{ number_format($loan->interest_rate, 1) }}%</strong> mensual.</p>
                <p class="mb-1">- Capitalización los días <strong>{{ $loan->start_date->format('d') }}</strong>.</p>
                <p class="mb-1">- Pagos aplican primero a interés, luego a capital.</p>
            </div>
            @endif
        </div>

        <!-- Signatures -->
        <div class="pt-8 text-center space-y-8">
            <div class="space-y-2">
                <div class="w-full h-px bg-black border-t border-black"></div>
                <p class="text-[9px] font-black uppercase tracking-widest">Firma Cliente</p>
                <p class="text-[9px] uppercase">{{ $loan->customer->name }}</p>
            </div>
            <div class="space-y-2 pt-4">
                <div class="w-full h-px bg-black border-t border-black"></div>
                <p class="text-[9px] font-black uppercase tracking-widest">Firma Prestamista</p>
            </div>
        </div>

        <div class="mt-8 text-center print:hidden">
            <button onclick="window.print()" class="bg-primary text-white px-6 py-3 rounded-2xl font-black text-sm uppercase tracking-widest hover:scale-105 active:scale-95 transition-all shadow-xl shadow-blue-500/20">
                Imprimir Ticket
            </button>
            <p class="text-slate-400 text-[10px] mt-4 font-bold uppercase tracking-widest">* Optimizado para ticket de 80mm</p>
        </div>
    </div>

    <style>
        @media print {
            @page {
                margin: 0;
                size: 80mm auto;
            }
            body * {
                visibility: hidden;
            }
            #receipt-content, #receipt-content * {
                visibility: visible;
                color: #000 !important;
            }
            #receipt-content {
                position: absolute;
                left: 0;
                top: 0;
                width: 80mm !important;
                max-width: 80mm !important;
                margin: 0 !important;
                padding: 4mm !important;
                box-shadow: none !important;
                border: none !important;
            }
            .print\:hidden {
                display: none !important;
            }
            table { page-break-inside: auto; }
            tr { page-break-inside: avoid; page-break-after: auto; }
        }
    </style>
</x-layouts.app>
