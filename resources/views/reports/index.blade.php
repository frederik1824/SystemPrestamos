<x-layouts.app>
    <div class="mb-10">
        <h2 class="text-4xl font-black text-slate-900 tracking-tighter uppercase mb-2">Reportes</h2>
        <p class="text-slate-500 font-medium">Resumen financiero y listado de transacciones.</p>
    </div>

    <x-ui.card>
        <div class="flex items-center justify-between mb-8">
            <h3 class="text-xl font-bold text-slate-900 tracking-tight">Cartera de Préstamos</h3>
            <button class="px-5 py-2 bg-emerald-500 text-white rounded-xl font-bold text-[10px] uppercase tracking-widest flex items-center gap-2 hover:bg-emerald-600 transition-colors shadow-md">
                <span class="material-symbols-outlined text-[18px]">download</span>
                Exportar CSV
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-slate-500 text-[10px] uppercase tracking-widest font-black border-b border-slate-100">
                        <th class="pb-4">ID</th>
                        <th class="pb-4">Cliente</th>
                        <th class="pb-4">Monto</th>
                        <th class="pb-4">Balance</th>
                        <th class="pb-4">Estado</th>
                        <th class="pb-4">Desde</th>
                        <th class="pb-4">Hasta</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($loans as $loan)
                        <tr class="text-sm">
                            <td class="py-4 text-slate-500 font-mono text-[10px]">#{{ str_pad($loan->id, 5, '0', STR_PAD_LEFT) }}</td>
                            <td class="py-4 font-bold text-slate-900">{{ $loan->customer->name }}</td>
                            <td class="py-4 text-slate-500 font-medium">${{ number_format($loan->amount, 2) }}</td>
                            <td class="py-4 font-black text-slate-900">${{ number_format($loan->balance, 2) }}</td>
                            <td class="py-4">
                                <span class="px-2 py-1 bg-slate-100 rounded-lg text-[9px] font-black uppercase tracking-tighter">
                                    {{ $loan->status }}
                                </span>
                            </td>
                            <td class="py-4 text-slate-500">{{ $loan->start_date?->format('d/m/Y') ?? 'N/A' }}</td>
                            <td class="py-4 text-slate-500">{{ $loan->estimated_end_date?->format('d/m/Y') ?? '---' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-ui.card>
</x-layouts.app>
