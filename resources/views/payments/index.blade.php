<x-layouts.app>
    <div class="mb-10 flex items-center justify-between">
        <div>
            <h2 class="text-4xl font-black text-slate-900 tracking-tighter uppercase mb-2">Historial de Pagos</h2>
            <p class="text-slate-500 font-medium">Registro global de todas las transacciones de cobro.</p>
        </div>
        <div class="flex items-center gap-3">
            <x-ui.stats-card 
                label="Recaudación Total" 
                value="${{ number_format(\App\Models\Payment::sum('amount'), 2) }}" 
                icon="account_balance_wallet" 
                color="green" 
                class="hidden md:flex" />
        </div>
    </div>

    <x-ui.card>
        <!-- Search and Filter -->
        <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <form action="{{ route('payments.index') }}" method="GET" class="relative max-w-md w-full">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-500">search</span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nombre de cliente..." class="w-full pl-12 pr-4 py-3 bg-slate-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-primary/20 transition-all font-bold text-slate-700">
            </form>
            
            <div class="flex items-center gap-2">
                <button class="px-4 py-2 bg-slate-100 text-slate-500 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-200 transition-all flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">filter_list</span>
                    Filtros
                </button>
                <button class="px-4 py-2 bg-primary/10 text-primary rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-primary hover:text-white transition-all flex items-center gap-2 border border-primary/20">
                    <span class="material-symbols-outlined text-[18px]">download</span>
                    Exportar
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-separate border-spacing-y-4">
                <thead>
                    <tr class="text-slate-500 text-xs uppercase tracking-widest font-bold">
                        <th class="px-4 py-2">Transacción</th>
                        <th class="px-4 py-2">Cliente</th>
                        <th class="px-4 py-2">Préstamo</th>
                        <th class="px-4 py-2 text-center">Fecha</th>
                        <th class="px-4 py-2 text-center">Método</th>
                        <th class="px-4 py-2 text-right">Monto</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        <tr class="bg-white hover:bg-slate-50/50 transition-colors shadow-sm rounded-2xl group border border-slate-100/50">
                            <td class="px-4 py-4 rounded-l-2xl">
                                <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">TRX-{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</span>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-primary/5 flex items-center justify-center text-primary font-bold text-xs">
                                        {{ substr($payment->loan->customer->name, 0, 2) }}
                                    </div>
                                    <p class="font-bold text-slate-900 text-sm italic uppercase">{{ $payment->loan->customer->name }}</p>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <a href="{{ route('loans.show', $payment->loan) }}" class="inline-flex items-center gap-2 px-3 py-1 bg-slate-100 rounded-lg text-[10px] font-black text-slate-500 uppercase tracking-widest hover:bg-primary/10 hover:text-primary transition-all">
                                    <span class="material-symbols-outlined text-[14px]">link</span>
                                    #{{ str_pad($payment->loan->id, 5, '0', STR_PAD_LEFT) }}
                                </a>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <span class="text-slate-700 font-bold text-xs">{{ $payment->payment_date->format('d/m/Y') }}</span>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <span class="px-3 py-1 border border-slate-200 text-slate-500 rounded-full text-[9px] font-black uppercase tracking-widest">
                                    {{ $payment->payment_method }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-right rounded-r-2xl">
                                <span class="font-black text-green-600 text-sm">${{ number_format($payment->amount, 2) }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-20">
                                <div class="max-w-xs mx-auto">
                                    <span class="material-symbols-outlined text-slate-200 text-6xl mb-4">receipt_long</span>
                                    <p class="text-slate-500 font-medium italic">No se han registrado pagos que coincidan con los criterios de búsqueda.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-8">
            {{ $payments->links() }}
        </div>
    </x-ui.card>
</x-layouts.app>
