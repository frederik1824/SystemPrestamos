<x-layouts.app>
    <div class="mb-10 flex items-center justify-between">
        <div>
            <h2 class="text-4xl font-black text-slate-900 tracking-tighter uppercase mb-2">Dashboard</h2>
            <p class="text-slate-500 font-medium">Resumen general del estado financiero de préstamos.</p>
        </div>
        <div class="flex items-center gap-2 bg-white p-2 rounded-2xl shadow-sm border border-slate-100">
            <span class="w-3 h-3 rounded-full bg-green-500 animate-pulse"></span>
            <span class="text-[10px] font-black uppercase tracking-widest text-slate-500">Sistema en Vivo</span>
        </div>
    </div>

    <!-- KPIs -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <x-ui.stats-card 
            label="Total Prestado" 
            value="${{ number_format($totalLent, 2) }}" 
            icon="payments" 
            trend="+12%" 
            color="primary" />
        
        <x-ui.stats-card 
            label="Total Recuperado" 
            value="${{ number_format($totalRecovered, 2) }}" 
            icon="currency_exchange" 
            trend="+18%" 
            color="green" />
        
        <x-ui.stats-card 
            label="En Mora" 
            value="${{ number_format($totalLate, 2) }}" 
            icon="warning" 
            trend="-5%" 
            trendType="down"
            color="amber" />
        
        <x-ui.stats-card 
            label="Beneficios Reales" 
            value="${{ number_format($totalEarnings, 2) }}" 
            icon="trending_up" 
            trend="+24%" 
            color="emerald" />
    </div>
    
    <!-- Payment Alerts -->
    @if(count($alerts) > 0)
        <div class="mb-10">
            <x-ui.card class="border-amber-100 bg-amber-50/20">
                <h3 class="text-[10px] font-black text-slate-900 tracking-[0.2em] uppercase mb-6 flex items-center gap-2">
                    <span class="material-symbols-outlined text-amber-600 animate-pulse">notifications_active</span>
                    Alertas de Pago Próximas y Vencidas
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach(array_slice($alerts, 0, 6) as $alert)
                        <div class="p-4 rounded-2xl bg-white border border-slate-100 shadow-sm flex items-center gap-4 group hover:shadow-md transition-all">
                            <div @class([
                                'w-10 h-10 rounded-xl flex items-center justify-center text-sm',
                                'bg-red-50 text-red-600' => $alert['type'] === 'late',
                                'bg-amber-50 text-amber-600' => $alert['type'] === 'due_today'
                            ])>
                                <span class="material-symbols-outlined">{{ $alert['type'] === 'late' ? 'error' : 'schedule' }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-[11px] font-black text-slate-900 truncate uppercase">{{ $alert['customer'] }}</p>
                                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-tighter mt-1">
                                    <span class="font-black {{ $alert['type'] === 'late' ? 'text-red-500' : 'text-amber-500' }}">
                                        {{ $alert['type'] === 'late' ? 'VENCIDA' : 'VENCE HOY' }}
                                    </span> 
                                    • Cuota #{{ $alert['installment_no'] }}
                                </p>
                            </div>
                            <a href="{{ route('loans.show', $alert['loan_id']) }}" class="w-8 h-8 flex items-center justify-center bg-slate-50 hover:bg-primary hover:text-white text-slate-400 rounded-lg transition-all">
                                <span class="material-symbols-outlined text-sm">visibility</span>
                            </a>
                        </div>
                    @endforeach
                </div>
                @if(count($alerts) > 6)
                    <p class="mt-4 text-center text-[10px] font-black uppercase text-slate-400 tracking-widest italic">+ {{ count($alerts) - 6 }} alertas adicionales pendientes de revisión</p>
                @endif
            </x-ui.card>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Collection Chart -->
        <div class="lg:col-span-2">
            <x-ui.card class="h-full">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900 tracking-tight">Recaudación Mensual</h3>
                        <p class="text-sm text-slate-500 font-medium mt-1">Histórico de cobros realizados.</p>
                    </div>
                </div>
                
                <div class="relative h-[300px]">
                    <canvas id="collectionChart"></canvas>
                </div>
            </x-ui.card>
        </div>

        <!-- Quick Actions & Stats -->
        <div class="space-y-8">
            <x-ui.card class="!bg-primary text-white overflow-hidden relative">
                <div class="relative z-10">
                    <h3 class="text-xl font-bold tracking-tight mb-2 uppercase italic">Operaciones</h3>
                    <p class="text-white/80 text-sm font-medium mb-8 italic">Gestione su cartera de forma eficiente.</p>

                    <div class="space-y-4">
                        <a href="{{ route('loans.create') }}" class="flex items-center justify-between p-4 bg-white rounded-2xl transition-all group shadow-sm hover:shadow-xl hover:-translate-y-1">
                            <div class="flex items-center gap-4">
                                <span class="material-symbols-outlined text-primary font-bold">add_circle</span>
                                <span class="font-black text-xs tracking-tight text-primary uppercase">Nuevo Préstamo</span>
                            </div>
                            <span class="material-symbols-outlined text-primary group-hover:translate-x-1 transition-transform">chevron_right</span>
                        </a>

                        <a href="{{ route('customers.create') }}" class="flex items-center justify-between p-4 bg-white rounded-2xl transition-all group shadow-sm hover:shadow-xl hover:-translate-y-1">
                            <div class="flex items-center gap-4">
                                <span class="material-symbols-outlined text-primary font-bold">person_add</span>
                                <span class="font-black text-xs tracking-tight text-primary uppercase">Registrar Cliente</span>
                            </div>
                            <span class="material-symbols-outlined text-primary group-hover:translate-x-1 transition-transform">chevron_right</span>
                        </a>
                    </div>
                </div>
                <div class="absolute -right-20 -bottom-20 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            </x-ui.card>

            <!-- Distribution Info -->
            <x-ui.card>
                <h3 class="text-sm font-black text-slate-900 tracking-widest uppercase mb-6 flex items-center gap-2">
                    <span class="w-2 h-6 bg-primary rounded-full"></span>
                    Estado de Cartera
                </h3>
                <div class="space-y-6">
                    <div>
                        <div class="flex justify-between text-xs font-bold uppercase tracking-tighter mb-2">
                            <span class="text-slate-500">Capital en Riesgo (Mora)</span>
                            <span class="text-amber-600">${{ number_format($totalLate, 0) }}</span>
                        </div>
                        <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-amber-500 rounded-full" style="width: {{ $totalLent > 0 ? ($totalLate / $totalLent * 100) : 0 }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-xs font-bold uppercase tracking-tighter mb-2">
                            <span class="text-slate-500">Retorno de Inversión</span>
                            <span class="text-green-600">{{ $totalLent > 0 ? number_format(($totalRecovered / $totalLent * 100), 1) : 0 }}%</span>
                        </div>
                        <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-green-500 rounded-full" style="width: {{ $totalLent > 0 ? ($totalRecovered / $totalLent * 100) : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </x-ui.card>
        </div>

        <!-- Recent Activity -->
        <div class="lg:col-span-3">
            <x-ui.card>
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900 tracking-tight">Últimos Movimientos</h3>
                        <p class="text-sm text-slate-500 font-medium mt-1">Transacciones de pago procesadas recientemente.</p>
                    </div>
                    <a href="{{ route('payments.index') }}" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-primary hover:text-white transition-all">Ver Historial</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-separate border-spacing-y-3">
                        <thead>
                            <tr class="text-slate-500 text-[10px] uppercase tracking-widest font-black border-b border-slate-50">
                                <th class="px-4 pb-2">Cliente</th>
                                <th class="px-4 pb-2">Préstamo</th>
                                <th class="px-4 pb-2 text-center">Fecha</th>
                                <th class="px-4 pb-2 text-right">Monto</th>
                                <th class="px-4 pb-2 text-center">Método</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lastPayments as $payment)
                                <tr class="bg-slate-50/50 hover:bg-slate-50 transition-colors rounded-2xl group">
                                    <td class="px-4 py-4 rounded-l-2xl">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-white border border-slate-200 flex items-center justify-center text-primary font-bold text-xs uppercase shadow-sm">
                                                {{ substr($payment->loan->customer->name, 0, 2) }}
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-900 text-sm group-hover:text-primary transition-colors">{{ $payment->loan->customer->name }}</p>
                                                <p class="text-[10px] text-slate-500 font-medium uppercase tracking-tighter">ID: {{ $payment->loan->customer->identification_id }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <a href="{{ route('loans.show', $payment->loan) }}" class="text-xs font-bold text-slate-600 hover:text-primary underline decoration-primary/30">#{{ str_pad($payment->loan->id, 5, '0', STR_PAD_LEFT) }}</a>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <span class="text-slate-500 font-bold text-xs">{{ $payment->payment_date->format('d/m/Y') }}</span>
                                    </td>
                                    <td class="px-4 py-4 text-right">
                                        <span class="font-black text-slate-900 text-sm">${{ number_format($payment->amount, 2) }}</span>
                                    </td>
                                    <td class="px-4 py-4 text-center rounded-r-2xl">
                                        <span class="px-3 py-1 bg-white border border-slate-100 text-slate-600 rounded-lg text-[9px] font-black uppercase tracking-widest shadow-sm">
                                            {{ $payment->payment_method }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-10 text-slate-500 font-medium italic">No se han registrado pagos recientemente.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-ui.card>
        </div>
    </div>

    <!-- Chart.js and Search Script -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Chart Implementation
            const ctx = document.getElementById('collectionChart');
            if (ctx) {
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: @json($chartLabels),
                        datasets: [
                            {
                                label: 'Recaudación ($)',
                                data: @json($chartData),
                                borderColor: '#3b82f6',
                                backgroundColor: 'rgba(59, 130, 246, 0.05)',
                                borderWidth: 3,
                                fill: true,
                                tension: 0.4,
                                pointRadius: 3,
                                pointBackgroundColor: '#3b82f6'
                            },
                            {
                                label: 'Beneficios ($)',
                                data: @json($chartProfitData),
                                borderColor: '#10b981',
                                backgroundColor: 'rgba(16, 185, 129, 0.05)',
                                borderWidth: 3,
                                fill: true,
                                tension: 0.4,
                                pointRadius: 3,
                                pointBackgroundColor: '#10b981'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: '#f1f5f9' },
                                ticks: { font: { size: 10, weight: 'bold' }, color: '#94a3b8' }
                            },
                            x: {
                                grid: { display: false },
                                ticks: { font: { size: 10, weight: 'bold' }, color: '#94a3b8' }
                            }
                        }
                    }
                });
            }

            // Global Search Implementation
            const searchInput = document.getElementById('navbar-search');
            const resultsContainer = document.getElementById('results-container');
            const searchDropdown = document.getElementById('search-results');
            const emptyState = document.getElementById('search-empty');
            let debounceTimer;

            searchInput.addEventListener('input', (e) => {
                clearTimeout(debounceTimer);
                const query = e.target.value.trim();

                if (query.length < 2) {
                    searchDropdown.classList.add('hidden');
                    return;
                }

                debounceTimer = setTimeout(() => {
                    fetch(`{{ route('api.search') }}?q=${encodeURIComponent(query)}`)
                        .then(res => res.json())
                        .then(data => {
                            resultsContainer.innerHTML = '';
                            searchDropdown.classList.remove('hidden');

                            if (data.length === 0) {
                                emptyState.classList.remove('hidden');
                                return;
                            }

                            emptyState.classList.add('hidden');
                            data.forEach(item => {
                                const element = document.createElement('a');
                                element.href = item.url;
                                element.className = 'flex items-center gap-3 p-4 hover:bg-slate-50 transition-colors group';
                                element.innerHTML = `
                                    <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 group-hover:bg-primary/10 group-hover:text-primary transition-colors">
                                        <span class="material-symbols-outlined text-[20px]">${item.icon}</span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-900">${item.title}</p>
                                        <p class="text-[10px] font-medium text-slate-500 uppercase tracking-widest">${item.subtitle}</p>
                                    </div>
                                    <span class="material-symbols-outlined text-slate-300 ml-auto text-sm group-hover:translate-x-1 transition-transform">chevron_right</span>
                                `;
                                resultsContainer.appendChild(element);
                            });
                        });
                }, 300);
            });

            // Keyboard Shortcuts
            document.addEventListener('keydown', (e) => {
                if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                    e.preventDefault();
                    searchInput.focus();
                }
                if (e.key === 'Escape') {
                    searchDropdown.classList.add('hidden');
                    searchInput.blur();
                }
            });

            // Close on click outside
            document.addEventListener('click', (e) => {
                if (!searchInput.contains(e.target) && !searchDropdown.contains(e.target)) {
                    searchDropdown.classList.add('hidden');
                }
            });
        });
    </script>
</x-layouts.app>
