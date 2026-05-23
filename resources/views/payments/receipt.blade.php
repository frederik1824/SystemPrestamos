<x-layouts.app>
    <div id="receipt-content" class="max-w-md mx-auto my-10 bg-white p-8 border border-slate-200 shadow-sm print:shadow-none print:border-none print:my-0 print:p-4">
        <!-- Receipt Header -->
        <div class="text-center mb-6 border-b-2 border-black pb-4 border-dashed">
            <h1 class="text-xl font-black text-black uppercase tracking-tighter">Comprobante de Pago</h1>
            <p class="text-black text-xs font-bold uppercase tracking-widest mt-1">SysPrestamos</p>
        </div>

        <!-- Receipt Content -->
        <div class="space-y-4 text-black">
            <div class="flex justify-between items-end border-b border-black/20 pb-1">
                <span class="text-[10px] font-black uppercase tracking-widest">No. Recibo</span>
                <span class="text-sm font-bold">#REC-{{ str_pad($payment->id, 5, '0', STR_PAD_LEFT) }}</span>
            </div>

            <div class="flex justify-between items-end border-b border-black/20 pb-1">
                <span class="text-[10px] font-black uppercase tracking-widest">Fecha</span>
                <span class="text-xs font-bold">{{ \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y h:i A') }}</span>
            </div>

            <div class="space-y-1 border-b border-black/20 pb-2">
                <span class="text-[10px] font-black uppercase tracking-widest block">Recibido de</span>
                <span class="text-sm font-black uppercase">{{ $payment->loan->customer->name }}</span>
            </div>

            <div class="flex justify-between items-end border-b border-black/20 pb-1">
                <span class="text-[10px] font-black uppercase tracking-widest">Préstamo</span>
                <span class="text-sm font-bold">#{{ $payment->loan_id }}</span>
            </div>
            
            <div class="flex justify-between items-end border-b border-black/20 pb-1">
                <span class="text-[10px] font-black uppercase tracking-widest">Método</span>
                <span class="text-sm font-bold">{{ $payment->payment_method }}</span>
            </div>

            @if($payment->observations)
            <div class="pt-2 pb-2 border-b border-black/20">
                <span class="text-[10px] font-black uppercase tracking-widest block mb-1">Concepto</span>
                <p class="text-xs font-medium">{{ $payment->observations }}</p>
            </div>
            @endif

            <div class="pt-4 mt-2 border-t-2 border-black flex justify-between items-baseline border-dashed">
                <span class="text-sm font-black uppercase">Monto Pagado</span>
                <span class="text-xl font-black">${{ number_format((float) $payment->amount, 2) }}</span>
            </div>

            <div class="pt-10 text-center space-y-2">
                <div class="w-full h-px bg-black mx-auto border-t border-black"></div>
                <p class="text-[10px] font-bold uppercase tracking-widest">Firma Autorizada</p>
                <p class="text-[8px] mt-4 uppercase">¡Gracias por su pago!</p>
            </div>
        </div>

        <div class="mt-10 text-center print:hidden">
            <button onclick="window.print()" class="bg-primary text-white px-6 py-3 rounded-2xl font-black text-sm uppercase tracking-widest hover:scale-105 active:scale-95 transition-all shadow-lg shadow-blue-500/20">
                Imprimir Ticket
            </button>
            <p class="text-slate-400 text-[10px] mt-4 font-medium italic">* Formato optimizado para ticket térmico de 80mm</p>
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
        }
    </style>
</x-layouts.app>
