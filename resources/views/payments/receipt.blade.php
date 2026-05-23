<x-layouts.app>
    <div class="max-w-md mx-auto my-10 bg-white p-8 border border-slate-200 shadow-sm print:shadow-none print:border-none print:my-0 print:p-0">
        <!-- Receipt Header -->
        <div class="text-center mb-8">
            <h1 class="text-2xl font-black text-slate-900 uppercase tracking-tighter">Comprobante de Pago</h1>
            <p class="text-slate-500 text-xs font-medium uppercase tracking-widest mt-1">Loan Management System</p>
        </div>

        <!-- Receipt Content -->
        <div class="space-y-6">
            <div class="flex justify-between items-end border-b border-slate-100 pb-2">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">No. Recibo</span>
                <span class="text-sm font-bold text-slate-900">#REC-{{ str_pad($payment->id, 5, '0', STR_PAD_LEFT) }}</span>
            </div>

            <div class="flex justify-between items-end border-b border-slate-100 pb-2">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Fecha</span>
                <span class="text-sm font-bold text-slate-900">{{ \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y h:i A') }}</span>
            </div>

            <div class="space-y-1">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block">Recibido de</span>
                <span class="text-lg font-black text-slate-900 uppercase">{{ $payment->loan->customer->name }}</span>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block">Referencia</span>
                    <span class="text-sm font-bold text-slate-900">Préstamo #{{ $payment->loan_id }}</span>
                </div>
                <div class="space-y-1 text-right">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block">Método</span>
                    <span class="text-sm font-bold text-slate-900">{{ $payment->payment_method }}</span>
                </div>
            </div>

            @if($payment->observations)
            <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Concepto</span>
                <p class="text-xs text-slate-700 leading-relaxed font-medium">{{ $payment->observations }}</p>
            </div>
            @endif

            <div class="pt-4 mt-4 border-t-2 border-slate-900 flex justify-between items-baseline">
                <span class="text-lg font-black text-slate-900 uppercase">Monto Pagado</span>
                <span class="text-3xl font-black text-primary">${{ number_format((float) $payment->amount, 2) }}</span>
            </div>

            <div class="pt-8 text-center space-y-4">
                <div class="w-48 h-px bg-slate-300 mx-auto"></div>
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Firma Autorizada</p>
            </div>
        </div>

        <div class="mt-10 text-center print:hidden">
            <button onclick="window.print()" class="bg-primary text-white px-6 py-3 rounded-2xl font-black text-sm uppercase tracking-widest hover:scale-105 active:scale-95 transition-all shadow-lg shadow-blue-500/20">
                Imprimir Recibo
            </button>
            <p class="text-slate-400 text-[10px] mt-4 font-medium italic">* Use Ctrl+P si el botón no responde</p>
        </div>
    </div>

    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            .max-w-md, .max-w-md * {
                visibility: visible;
            }
            .max-w-md {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                max-width: 100%;
                border: none !important;
                box-shadow: none !important;
            }
            .print\:hidden {
                display: none !important;
            }
        }
    </style>
</x-layouts.app>
