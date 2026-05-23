<x-layouts.app>
    <!-- Header Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="p-6 bg-white rounded-3xl border border-slate-100 shadow-sm flex items-center gap-6">
            <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-3xl">account_balance_wallet</span>
            </div>
            <div>
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Préstamos Activos</p>
                <p class="text-2xl font-black text-slate-900">{{ $stats['total_active'] }}</p>
            </div>
        </div>
        
        <div class="p-6 bg-white rounded-3xl border border-slate-100 shadow-sm flex items-center gap-6">
            <div class="w-14 h-14 rounded-2xl bg-red-50 text-red-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-3xl">emergency_home</span>
            </div>
            <div>
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">En Mora (Retrasados)</p>
                <p class="text-2xl font-black text-red-600">{{ $stats['total_late'] }}</p>
            </div>
        </div>

        <div class="p-6 bg-white rounded-3xl border border-slate-100 shadow-sm flex items-center gap-6">
            <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-3xl">monetization_on</span>
            </div>
            <div>
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Capital en Calle</p>
                <p class="text-2xl font-black text-slate-900">${{ number_format($stats['total_capital'], 0) }}</p>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-6 bg-white p-4 rounded-3xl border border-slate-100 shadow-sm">
        <form action="{{ route('loans.index') }}" method="GET" class="flex-1 flex flex-col md:flex-row items-center gap-4 w-full">
            <div class="relative flex-1 w-full">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400">search</span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por cliente, cédula o ID..." class="w-full pl-12 pr-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm focus:ring-2 focus:ring-primary/20 transition-all font-medium">
            </div>
            
            <div class="flex items-center gap-2 w-full md:w-auto">
                <select name="status" onchange="this.form.submit()" class="px-6 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-xs font-black uppercase tracking-widest text-slate-600 focus:ring-2 focus:ring-primary/20 transition-all">
                    <option value="">Todos los Estados</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Activos</option>
                    <option value="late" {{ request('status') === 'late' ? 'selected' : '' }}>En Mora</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Pagados</option>
                </select>
                
                @if(request()->anyFilled(['search', 'status']))
                    <a href="{{ route('loans.index') }}" class="p-3 bg-red-50 text-red-500 rounded-2xl hover:bg-red-500 hover:text-white transition-all shadow-sm">
                        <span class="material-symbols-outlined text-[20px]">filter_alt_off</span>
                    </a>
                @endif
            </div>
        </form>
        
        <a href="{{ route('loans.create') }}" class="w-full md:w-auto px-8 py-4 bg-primary text-white rounded-2xl font-black uppercase text-xs tracking-[0.2em] shadow-lg shadow-primary/20 hover:shadow-xl hover:-translate-y-1 transition-all flex items-center justify-center gap-2">
            <span class="material-symbols-outlined text-[20px]">add_circle</span>
            Nuevo Préstamo
        </a>
    </div>

    <!-- Loans List -->
    <div class="space-y-4">
        @forelse($loans as $loan)
            <x-ui.card class="!p-0 overflow-hidden hover:border-primary/30 transition-all group">
                <div class="flex flex-col lg:flex-row">
                    <!-- Customer Section -->
                    <div class="lg:w-1/4 p-6 bg-slate-50/50 border-r border-slate-100">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-12 h-12 rounded-2xl bg-white border border-slate-200 flex items-center justify-center text-primary font-black text-sm uppercase shadow-sm">
                                {{ substr($loan->customer->name, 0, 2) }}
                            </div>
                            <div class="min-w-0">
                                <h4 class="font-black text-slate-900 truncate uppercase text-sm group-hover:text-primary transition-colors">{{ $loan->customer->name }}</h4>
                                <p class="text-[10px] font-bold text-slate-500 tracking-tighter">{{ $loan->customer->identification_id }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-1 bg-white border border-slate-100 rounded-lg text-[9px] font-black uppercase tracking-widest text-slate-500">#{{ str_pad($loan->id, 5, '0', STR_PAD_LEFT) }}</span>
                            <span @class([
                                'px-2 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest',
                                'bg-blue-100 text-blue-700' => $loan->status === 'active',
                                'bg-green-100 text-green-700' => $loan->status === 'paid',
                                'bg-red-100 text-red-700' => $loan->status === 'late',
                                'bg-slate-100 text-slate-500' => $loan->status === 'cancelled',
                            ])>
                                {{ $loan->status }}
                            </span>
                        </div>
                    </div>

                    <!-- Financial Stats -->
                    <div class="flex-1 p-6 grid grid-cols-2 md:grid-cols-4 gap-6 items-center">
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Monto Inicial</p>
                            <p class="text-sm font-black text-slate-900">${{ number_format($loan->amount, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Balance Pendiente</p>
                            <p class="text-sm font-black text-primary">${{ number_format($loan->balance, 2) }}</p>
                        </div>
                        <div class="col-span-2">
                            <div class="flex justify-between items-center mb-2">
                                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Progreso de Pago</p>
                                <span class="text-[10px] font-black text-slate-900">{{ $loan->getRepaymentProgress() }}%</span>
                            </div>
                            <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full bg-primary rounded-full transition-all duration-1000" style="width: {{ $loan->getRepaymentProgress() }}%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Date & Actions -->
                    <div class="lg:w-1/5 p-6 bg-slate-50/30 flex items-center justify-between lg:flex-col lg:justify-center gap-4 lg:border-l lg:border-slate-100">
                        <div class="text-center">
                            @php $nextDue = $loan->getNextDueDate(); @endphp
                            @if($nextDue && $loan->status !== 'paid')
                                <p class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-1">Próximo Vence</p>
                                <p @class([
                                    'text-xs font-black uppercase',
                                    'text-red-500' => $nextDue->isPast() && !$nextDue->isToday(),
                                    'text-amber-600' => $nextDue->isToday(),
                                    'text-slate-900' => $nextDue->isFuture()
                                ])>{{ $nextDue->format('d/m/Y') }}</p>
                            @else
                                <p class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-1">Fecha de Término</p>
                                <p class="text-xs font-black text-slate-500 uppercase italic">Finalizado</p>
                            @endif
                        </div>
                        <a href="{{ route('loans.show', $loan) }}" class="w-full lg:w-auto px-6 py-3 bg-white border border-slate-200 text-slate-600 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-primary hover:border-primary hover:text-white transition-all shadow-sm flex items-center justify-center gap-2 group/btn">
                            Gestionar
                            <span class="material-symbols-outlined text-[16px] group-hover/btn:translate-x-1 transition-transform">arrow_forward</span>
                        </a>
                    </div>
                </div>
            </x-ui.card>
        @empty
            <div class="p-20 bg-white rounded-3xl border-2 border-dashed border-slate-100 flex flex-col items-center justify-center text-center">
                <div class="w-20 h-20 rounded-full bg-slate-50 flex items-center justify-center text-slate-300 mb-6">
                    <span class="material-symbols-outlined text-4xl">inventory_2</span>
                </div>
                <h3 class="text-xl font-black text-slate-900 uppercase tracking-tighter mb-2">No se encontraron préstamos</h3>
                <p class="text-slate-500 font-medium max-w-xs">Ajuste los filtros o realice una nueva búsqueda para encontrar lo que busca.</p>
                <a href="{{ route('loans.index') }}" class="mt-8 text-primary font-black uppercase text-[10px] tracking-widest hover:underline italic">Limpiar todos los filtros</a>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-10">
        {{ $loans->links() }}
    </div>
</x-layouts.app>
