<x-layouts.app>
    <div class="max-w-4xl mx-auto my-10 bg-white p-12 border border-slate-100 shadow-sm print:shadow-none print:border-none print:my-0 print:p-0">
        <!-- Document Header -->
        <div class="flex justify-between items-start mb-12">
            <div>
                <h1 class="text-3xl font-black text-slate-900 uppercase tracking-tighter">Resumen de Préstamo</h1>
                <p class="text-slate-500 text-sm font-bold uppercase tracking-widest mt-1">NÚMERO DE CONTRATO: #CON-{{ str_pad($loan->id, 6, '0', STR_PAD_LEFT) }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm font-black text-slate-900 uppercase tracking-tighter">Fecha de Apertura</p>
                <p class="text-slate-500 text-sm font-bold uppercase tracking-widest">{{ \Carbon\Carbon::parse($loan->start_date)->format('d/m/Y') }}</p>
            </div>
        </div>

        <!-- Section 1: Customer Data -->
        <div class="bg-slate-50 p-6 rounded-3xl border border-slate-100 mb-8">
            <h2 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Información del Prestatario</h2>
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-widest block mb-1">Nombre Completo</span>
                    <span class="text-base font-black text-slate-900 uppercase">{{ $loan->customer->name }}</span>
                </div>
                <div>
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-widest block mb-1">Cédula / Identificación</span>
                    <span class="text-base font-black text-slate-900 uppercase">{{ $loan->customer->identification }}</span>
                </div>
            </div>
        </div>

        <!-- Section 2: Financial Terms -->
        <div class="mb-12">
            <h2 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6">Condiciones Financieras</h2>
            <div class="grid grid-cols-4 gap-8">
                <div class="border-l-2 border-slate-200 pl-4">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Monto Prestado</span>
                    <span class="text-xl font-black text-slate-900 tracking-tight">${{ number_format((float) $loan->amount, 2) }}</span>
                </div>
                <div class="border-l-2 border-slate-200 pl-4">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Tasa Interés (%)</span>
                    <span class="text-xl font-black text-slate-900 tracking-tight">{{ $loan->interest_rate }}%</span>
                </div>
                <div class="border-l-2 border-slate-200 pl-4">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Modalidad Pago</span>
                    <span class="text-xl font-black text-slate-900 uppercase tracking-tighter">{{ $loan->payment_modality }}</span>
                </div>
                <div class="border-l-2 border-slate-900 pl-4">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Total a Pagar (Inicial)</span>
                    <span class="text-xl font-black text-primary tracking-tight">${{ number_format($loan->amount + $loan->calculateInterests(), 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Section 3: Amortization Table / Open Info -->
        <div class="mb-16">
            @if($loan->type === 'installments')
            <h2 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-6 pb-2 border-b-2 border-slate-100">Calendario Proyectado de Cuotas</h2>
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-900 text-white rounded-t-xl overflow-hidden uppercase text-[10px] font-black tracking-widest">
                        <th class="py-4 px-6 rounded-tl-xl">No.</th>
                        <th class="py-4 px-6">Fecha de Vencimiento</th>
                        <th class="py-4 px-6 text-right rounded-tr-xl">Monto de Cuota</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 border-x border-b border-slate-100 rounded-b-xl">
                    @foreach($loan->getAmortizationSchedule() as $installment)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="py-4 px-6 text-sm font-black text-slate-900">#{{ $installment['number'] }}</td>
                        <td class="py-4 px-6 text-sm font-bold text-slate-600 uppercase">{{ $installment['due_date']->format('d/m/Y') }}</td>
                        <td class="py-4 px-6 text-sm font-black text-slate-900 text-right tracking-tight">${{ number_format((float) $installment['amount'], 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <h2 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-6 pb-2 border-b-2 border-slate-100">Información de Pagos Varios</h2>
            <div class="p-8 bg-slate-50 rounded-3xl border border-slate-100">
                <p class="text-slate-700 text-base leading-relaxed mb-6">
                    Este préstamo bajo la modalidad de <strong>Pagos Varios</strong> no tiene un calendario de cuotas fijo. El prestatario se compromete a realizar abonos a la deuda según su capacidad, bajo los siguientes términos:
                </p>
                <ul class="space-y-4">
                    <li class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-primary text-sm mt-1">check_circle</span>
                        <span class="text-sm text-slate-600 font-medium">Se aplicará un interés del <strong>{{ number_format($loan->interest_rate, 1) }}%</strong> mensual sobre el saldo insoluto.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-primary text-sm mt-1">check_circle</span>
                        <span class="text-sm text-slate-600 font-medium">La capitalización de intereses se realizará el día <strong>{{ $loan->start_date->format('d') }}</strong> de cada mes.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-primary text-sm mt-1">check_circle</span>
                        <span class="text-sm text-slate-600 font-medium">Los pagos recibidos se aplicarán primero a los intereses acumulados y luego al capital principal.</span>
                    </li>
                </ul>
            </div>
            @endif
        </div>

        <!-- Footer / Signatures -->
        <div class="grid grid-cols-2 gap-24 pt-12">
            <div class="text-center space-y-4">
                <div class="w-full h-px bg-slate-300"></div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">Acepto los términos del préstamo</p>
                <p class="text-sm font-black text-slate-900 uppercase">{{ $loan->customer->name }}</p>
            </div>
            <div class="text-center space-y-4">
                <div class="w-full h-px bg-slate-300"></div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Representante Legal</p>
                <p class="text-sm font-black text-slate-900 uppercase">Firma del Prestamista</p>
            </div>
        </div>

        <div class="mt-16 text-center print:hidden">
            <button onclick="window.print()" class="bg-primary text-white px-8 py-4 rounded-3xl font-black text-sm uppercase tracking-widest hover:scale-105 active:scale-95 transition-all shadow-xl shadow-blue-500/20">
                Imprimir Documento Completo
            </button>
            <p class="text-slate-400 text-[10px] mt-4 font-bold uppercase tracking-widest">* Este documento sirve como constancia legal de la deuda</p>
        </div>
    </div>

    <style>
        @media print {
            body * {
                visibility: hidden !important;
            }
            .max-w-4xl, .max-w-4xl * {
                visibility: visible !important;
            }
            .max-w-4xl {
                position: absolute;
                left: 0;
                top: 0;
                width: 100% !important;
                max-width: 100% !important;
                border: none !important;
                box-shadow: none !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .print\:hidden {
                display: none !important;
            }
            tr { page-break-inside: avoid; }
        }
    </style>
</x-layouts.app>
