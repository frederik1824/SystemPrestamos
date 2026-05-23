<x-layouts.app>
    <div class="flex items-center justify-between mb-10">
        <div>
            <h2 class="text-4xl font-black text-slate-900 tracking-tighter uppercase mb-2">Clientes</h2>
            <p class="text-slate-500 font-medium">Gestión y control de la base de clientes.</p>
        </div>
        <a href="{{ route('customers.create') }}" class="px-6 py-3 bg-primary text-white rounded-2xl font-bold uppercase text-xs tracking-widest shadow-lg hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex items-center gap-2">
            <span class="material-symbols-outlined">person_add</span>
            Nuevo Cliente
        </a>
    </div>

    <x-ui.card>
        <!-- Search and Filter -->
        <div class="mb-8">
            <form action="{{ route('customers.index') }}" method="GET" class="relative max-w-md">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-500">search</span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nombre, cédula o teléfono..." class="w-full pl-12 pr-4 py-3 bg-slate-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-primary/20 transition-all">
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-separate border-spacing-y-4">
                <thead>
                    <tr class="text-slate-500 text-xs uppercase tracking-widest font-bold">
                        <th class="px-4 py-2">Cliente</th>
                        <th class="px-4 py-2">Cédula / ID</th>
                        <th class="px-4 py-2">Contacto</th>
                        <th class="px-4 py-2 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                        <tr class="bg-white hover:bg-slate-50 transition-colors shadow-sm rounded-2xl group">
                            <td class="px-4 py-4 rounded-l-2xl">
                                <div class="flex items-center gap-4">
                                    <div class="relative group">
                                        <img src="{{ $customer->photo_url }}" alt="{{ $customer->name }}" class="w-12 h-12 rounded-2xl object-cover ring-2 ring-slate-100 group-hover:ring-primary/20 transition-all shadow-sm">
                                        <div class="absolute inset-0 rounded-2xl bg-black/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-900 text-sm leading-tight transition-colors group-hover:text-primary">{{ $customer->name }}</p>
                                        <p class="text-[10px] text-slate-500 font-medium uppercase tracking-tighter mt-1">{{ $customer->email ?? 'Sin correo' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <span class="text-slate-700 font-bold text-sm tracking-tight">{{ $customer->identification_id }}</span>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-2 text-slate-500">
                                    <span class="material-symbols-outlined text-[16px]">call</span>
                                    <span class="text-xs font-medium">{{ $customer->phone }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-right rounded-r-2xl">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('customers.show', $customer) }}" class="p-2 hover:bg-primary/10 text-slate-500 hover:text-primary rounded-xl transition-colors">
                                        <span class="material-symbols-outlined text-[20px]">visibility</span>
                                    </a>
                                    <a href="{{ route('customers.edit', $customer) }}" class="p-2 hover:bg-blue-50 text-slate-500 hover:text-blue-600 rounded-xl transition-colors">
                                        <span class="material-symbols-outlined text-[20px]">edit</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-10 text-slate-500 font-medium italic">No se encontraron clientes.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-8">
            {{ $customers->links() }}
        </div>
    </x-ui.card>
</x-layouts.app>
