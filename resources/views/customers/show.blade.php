<x-layouts.app>
    <div class="mb-10 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('customers.index') }}" class="w-10 h-10 rounded-xl bg-white border border-slate-100 flex items-center justify-center text-slate-500 hover:text-primary transition-colors">
                <span class="material-symbols-outlined">arrow_back</span>
            </a>
            <div>
                <h2 class="text-4xl font-black text-slate-900 tracking-tighter uppercase mb-1">Detalle del Cliente</h2>
                <p class="text-slate-500 font-medium text-sm italic">Información completa y estado del cliente.</p>
            </div>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="{{ route('customers.edit', $customer) }}" class="px-6 py-3 bg-white border border-slate-200 text-slate-600 rounded-2xl font-bold uppercase text-xs tracking-widest shadow-sm hover:shadow-md hover:-translate-y-1 transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px]">edit</span>
                Editar Perfil
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Profile Info -->
        <div class="lg:col-span-1 space-y-6">
            <x-ui.card class="relative">
                <div class="flex flex-col items-center mb-8">
                    <div class="relative mb-4 group">
                        <img src="{{ $customer->photo_url }}" alt="{{ $customer->name }}" class="w-32 h-32 rounded-[2.5rem] object-cover ring-4 ring-slate-100 shadow-xl group-hover:ring-primary/20 transition-all">
                        <div class="absolute inset-0 rounded-[2.5rem] bg-black/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    </div>
                    <h3 class="text-2xl font-black text-slate-900 text-center uppercase tracking-tight">{{ $customer->name }}</h3>
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-500 mt-1">ID: #{{ str_pad($customer->id, 5, '0', STR_PAD_LEFT) }}</p>
                </div>

                <div class="space-y-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 mb-2 drop-shadow-sm">Cédula / ID</label>
                        <p class="text-slate-900 font-bold bg-slate-50 p-3 rounded-xl border border-slate-100/50 shadow-sm">{{ $customer->identification_id }}</p>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 mb-2 drop-shadow-sm">Teléfono Principal</label>
                        <p class="text-slate-900 font-bold bg-slate-50 p-3 rounded-xl border border-slate-100/50 shadow-sm flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary text-sm">call</span>
                            {{ $customer->phone }}
                        </p>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 mb-2 drop-shadow-sm">Email</label>
                        <p class="text-slate-900 font-bold bg-slate-50 p-3 rounded-xl border border-slate-100/50 shadow-sm flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary text-sm">mail</span>
                            {{ $customer->email ?? 'N/A' }}
                        </p>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 mb-2 drop-shadow-sm">Dirección</label>
                        <p class="text-slate-900 font-bold bg-slate-50 p-3 rounded-xl border border-slate-100/50 shadow-sm whitespace-pre-wrap">{{ $customer->address ?? 'Sin dirección registrada' }}</p>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 mb-2 drop-shadow-sm italic">Garantía / Aval</label>
                        <p class="text-slate-900 font-bold bg-slate-50 p-3 rounded-xl border border-slate-100/50 shadow-sm whitespace-pre-wrap">{{ $customer->guarantee ?? 'Ninguna especificada' }}</p>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 mb-2 drop-shadow-sm italic">Notas Adicionales</label>
                        <p class="text-slate-900 font-bold bg-slate-50 p-3 rounded-xl border border-slate-100/50 shadow-sm whitespace-pre-wrap italic opacity-80">{{ $customer->notes ?? 'Sin comentarios' }}</p>
                    </div>
                </div>
            </x-ui.card>
        </div>

        <!-- Loans History -->
        <div class="lg:col-span-2 space-y-6">
            <x-ui.card>
                <div class="flex items-center justify-between mb-8 pb-4 border-b border-slate-50">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary">account_balance_wallet</span>
                            Historial de Préstamos
                        </h3>
                    </div>
                    <a href="{{ route('loans.create', ['customer_id' => $customer->id]) }}" class="px-4 py-2 bg-primary/10 text-primary rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-primary hover:text-white transition-all">
                        Nuevo Préstamo
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-slate-500 text-[10px] uppercase tracking-widest font-black">
                                <th class="pb-4">No.</th>
                                <th class="pb-4">Monto</th>
                                <th class="pb-4">Saldo</th>
                                <th class="pb-4">Estado</th>
                                <th class="pb-4 text-right">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($customer->loans as $loan)
                                <tr class="group hover:bg-slate-50 transition-colors">
                                    <td class="py-4 font-bold text-slate-500 text-xs">#{{ str_pad($loan->id, 5, '0', STR_PAD_LEFT) }}</td>
                                    <td class="py-4">
                                        <p class="font-black text-slate-900 text-sm">${{ number_format($loan->amount, 2) }}</p>
                                        <p class="text-[10px] text-slate-500 font-medium uppercase tracking-tighter">{{ $loan->created_at->format('d/m/Y') }}</p>
                                    </td>
                                    <td class="py-4">
                                        <span class="font-bold text-primary text-sm">${{ number_format($loan->balance, 2) }}</span>
                                    </td>
                                    <td class="py-4">
                                        @php
                                            $statusColors = [
                                                'active' => 'bg-blue-100 text-blue-700',
                                                'paid' => 'bg-green-100 text-green-700',
                                                'late' => 'bg-red-100 text-red-700',
                                                'cancelled' => 'bg-slate-100 text-slate-700',
                                            ];
                                        @endphp
                                        <span class="px-2 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest {{ $statusColors[$loan->status] }}">
                                            {{ $loan->status === 'active' ? 'En Curso' : ($loan->status === 'late' ? 'En Mora' : ($loan->status === 'paid' ? 'Pagado' : 'Cancelado')) }}
                                        </span>
                                    </td>
                                    <td class="py-4 text-right">
                                        <a href="{{ route('loans.show', $loan) }}" class="p-2 hover:bg-primary/10 text-slate-500 hover:text-primary rounded-xl transition-colors">
                                            <span class="material-symbols-outlined text-[18px]">visibility</span>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-10 text-center text-slate-500 font-medium italic">El cliente no tiene préstamos registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-ui.card>
        </div>
    </div>
</x-layouts.app>
