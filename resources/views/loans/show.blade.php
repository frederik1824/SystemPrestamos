<x-layouts.app>
    <div class="mb-10 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('loans.index') }}" class="w-10 h-10 rounded-xl bg-white border border-slate-100 flex items-center justify-center text-slate-500 hover:text-primary transition-colors">
                <span class="material-symbols-outlined">arrow_back</span>
            </a>
            <div>
                <h2 class="text-4xl font-black text-slate-900 tracking-tighter uppercase mb-1">Detalle del Préstamo</h2>
                <p class="text-slate-500 font-medium text-sm uppercase tracking-widest">ID: #{{ str_pad($loan->id, 5, '0', STR_PAD_LEFT) }} — {{ $loan->customer->name }}</p>
            </div>
        </div>
        
        <div class="flex items-center gap-3">
            @php
                $statusColors = [
                    'active' => 'bg-blue-100 text-blue-700',
                    'paid' => 'bg-green-100 text-green-700',
                    'late' => 'bg-red-100 text-red-700',
                    'cancelled' => 'bg-slate-100 text-slate-700',
                ];
            @endphp
            <span class="px-4 py-2 rounded-2xl text-xs font-black uppercase tracking-widest {{ $statusColors[$loan->status] }}">
                {{ $loan->status === 'active' ? 'En Curso' : ($loan->status === 'late' ? 'En Mora' : ($loan->status === 'paid' ? 'Pagado' : 'Cancelado')) }}
            </span>
            <a href="{{ route('loans.document', $loan) }}" target="_blank" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-2xl font-bold uppercase text-[10px] tracking-widest hover:bg-slate-50 transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">print</span>
                Imprimir Resumen
            </a>
            @if($loan->status !== 'paid')
                <button onclick="settleLoan()" class="px-6 py-2 bg-amber-500 text-white rounded-2xl font-bold uppercase text-[10px] tracking-widest shadow-lg hover:bg-amber-600 transition-all flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">handshake</span>
                    Saldar Préstamo
                </button>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Stats and Details -->
        <div class="lg:col-span-2 space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <x-ui.card class="!bg-indigo-600 text-white border-none">
                    <p class="text-[10px] font-black uppercase tracking-widest text-indigo-100 mb-2">Monto Prestado</p>
                    <p class="text-2xl font-black text-white">${{ number_format($loan->amount, 2) }}</p>
                </x-ui.card>

                <x-ui.card class="!bg-slate-900 text-white border-none">
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-300 mb-2">Monto Total + Interés</p>
                    <p class="text-2xl font-black text-white">${{ number_format($loan->amount + $loan->calculateInterests(), 2) }}</p>
                </x-ui.card>
                
                <x-ui.card class="!bg-primary text-white border-none">
                    <p class="text-[10px] font-black uppercase tracking-widest text-white/90 mb-2">Saldo Pendiente</p>
                    <p class="text-2xl font-black text-white">${{ number_format($loan->balance, 2) }}</p>
                </x-ui.card>

                <x-ui.card>
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-500 mb-2">Tasa de Interés</p>
                    <p class="text-2xl font-black text-slate-900">{{ number_format($loan->interest_rate, 1) }}% <span class="text-xs text-slate-500 font-bold uppercase tracking-tighter ml-1">{{ $loan->interest_type }}</span></p>
                </x-ui.card>
            </div>

            <!-- Payment History -->
            <x-ui.card>
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-xl font-bold text-slate-900 tracking-tight">Historial de Pagos</h3>
                    <div class="px-3 py-1 bg-slate-100 rounded-xl text-[10px] font-black text-slate-500 uppercase tracking-widest">
                        Total: {{ $loan->payments->count() }} pagos
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-slate-500 text-[10px] uppercase tracking-widest font-black border-b border-slate-50">
                                <th class="pb-4">Fecha</th>
                                <th class="pb-4">Método</th>
                                <th class="pb-4 text-right">Monto</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($loan->payments as $payment)
                                <tr class="group">
                                    <td class="py-4">
                                        <p class="font-bold text-slate-900 text-sm">{{ $payment->payment_date->format('d/m/Y') }}</p>
                                        <p class="text-[10px] text-slate-500 font-medium italic">{{ $payment->observations ?? 'Sin observaciones' }}</p>
                                    </td>
                                    <td class="py-4">
                                        <span class="text-xs font-bold text-slate-600 uppercase tracking-tighter">{{ $payment->payment_method }}</span>
                                    </td>
                                    <td class="py-4 text-right flex items-center justify-end gap-3">
                                        <span class="font-black text-primary text-sm">${{ number_format($payment->amount, 2) }}</span>
                                        <a href="{{ route('payments.receipt', $payment) }}" target="_blank" class="w-8 h-8 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:text-primary transition-all group-hover:border-primary/20" title="Imprimir Comprobante">
                                            <span class="material-symbols-outlined text-sm">print</span>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-10 text-center text-slate-500 font-medium italic">No se han registrado pagos aún.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-ui.card>

            <!-- Calendario de Cuotas (Solo para préstamos por cuotas) -->
            @if($loan->type === 'installments')
            <x-ui.card class="mt-8">
                <div class="flex items-center justify-between mb-8 pb-4 border-b border-slate-100/50">
                    <h3 class="text-xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">calendar_month</span>
                        Calendario de Cuotas
                    </h3>
                    <span class="px-3 py-1 bg-slate-100 text-slate-500 rounded-lg text-[10px] font-black uppercase tracking-widest border border-slate-200/50">
                        {{ $loan->installments }} Cuotas Totales
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-slate-500 text-[10px] uppercase tracking-widest font-black">
                                <th class="pb-4">No.</th>
                                <th class="pb-4 text-center">Fecha Vencimiento</th>
                                <th class="pb-4 text-right">Monto Cuota</th>
                                <th class="pb-4 text-right">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($loan->getAmortizationSchedule() as $installment)
                                <tr class="group hover:bg-slate-50/50 transition-colors">
                                    <td class="py-4 font-bold text-slate-400 text-xs">#{{ str_pad($installment['number'], 2, '0', STR_PAD_LEFT) }}</td>
                                    <td class="py-4 text-center">
                                        <span class="text-xs font-bold {{ $installment['status'] === 'late' ? 'text-red-600' : 'text-slate-700' }}">
                                            {{ $installment['due_date']->format('d/m/Y') }}
                                        </span>
                                    </td>
                                    <td class="py-4 text-right">
                                        <div class="flex flex-col items-end">
                                            <span class="font-black text-slate-900 text-sm">${{ number_format($installment['amount'], 2) }}</span>
                                            @if($installment['mora'] > 0)
                                                <span class="text-[9px] font-black text-red-600">+ ${{ number_format($installment['mora'], 2) }} mora</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-4 text-right">
                                        @php
                                            $statStyles = [
                                                'paid' => 'bg-green-100 text-green-700',
                                                'late' => 'bg-red-100 text-red-700',
                                                'pending' => 'bg-slate-100 text-slate-600',
                                            ];
                                            $statLabels = [
                                                'paid' => 'Pagada',
                                                'late' => 'Vencida',
                                                'pending' => 'Pendiente',
                                            ];
                                        @endphp
                                        <span class="px-2 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest {{ $statStyles[$installment['status']] }}">
                                            {{ $statLabels[$installment['status']] }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-ui.card>
            @else
            <x-ui.card class="mt-8">
                <div class="flex items-center justify-between mb-8 pb-4 border-b border-slate-100/50">
                    <h3 class="text-xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">analytics</span>
                        Resumen de Deuda Dinámica
                    </h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-slate-50 p-6 rounded-3xl border border-slate-100">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Tasa de Interés Mensual</p>
                        <p class="text-2xl font-black text-slate-900">{{ number_format($loan->interest_rate, 1) }}%</p>
                        <p class="text-[9px] text-slate-500 font-bold uppercase mt-2 italic">* Se capitaliza el primer día de cada mes sobre el saldo pendiente.</p>
                    </div>
                    <div class="bg-slate-50 p-6 rounded-3xl border border-slate-100">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Última Capitalización</p>
                        <p class="text-2xl font-black text-slate-900">{{ $loan->last_interest_at ? $loan->last_interest_at->format('d/m/Y') : 'Pendiente' }}</p>
                        <p class="text-[9px] text-slate-500 font-bold uppercase mt-2 italic">* Próximo cargo: {{ ($loan->last_interest_at ?? $loan->start_date)?->addMonth()->format('d/m/Y') ?? 'N/A' }}</p>
                    </div>
                </div>
            </x-ui.card>
            @endif
        </div>

        <!-- Register Payment Sidebar -->
        <div class="space-y-8">
            <x-ui.card class="bg-slate-50 border-2 border-slate-100 shadow-none">
                <h3 class="text-xl font-black text-slate-900 tracking-tighter uppercase mb-6 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">add_card</span>
                    Registrar Pago
                </h3>

                <form action="{{ route('payments.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="loan_id" value="{{ $loan->id }}">
                    
                    @if($loan->type === 'installments')
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-500">Seleccionar Cuota(s)</label>
                        <div class="space-y-1 max-h-56 overflow-y-auto custom-scrollbar p-1 bg-white border border-slate-200 rounded-2xl shadow-inner-sm">
                            @foreach($loan->getAmortizationSchedule() as $inst)
                                @if($inst['status'] !== 'paid')
                                    <label class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 cursor-pointer transition-colors border border-transparent hover:border-slate-100 group/item">
                                        <input type="checkbox" name="installments_selected[]" value="{{ $inst['number'] }}" data-amount="{{ $inst['total'] }}" class="w-5 h-5 rounded-lg text-primary focus:ring-primary/20 border-slate-300 installment-checkbox">
                                        <div class="flex-1">
                                            <p class="text-[10px] font-bold text-slate-500">{{ $inst['due_date']->format('d/m/Y') }} • ${{ number_format($inst['total'], 2) }}</p>
                                            @if($inst['mora'] > 0)
                                                <p class="text-[8px] font-black text-red-500 uppercase tracking-tighter">Incluye ${{ number_format($inst['mora'], 2) }} de mora</p>
                                            @endif
                                        </div>
                                        <span @class([
                                            'px-2 py-0.5 rounded text-[8px] font-black uppercase tracking-widest group-hover/item:opacity-100 opacity-60 transition-opacity',
                                            'bg-red-100 text-red-600 font-black' => $inst['status'] === 'late',
                                            'bg-slate-100 text-slate-500' => $inst['status'] === 'pending'
                                        ])>
                                            {{ $inst['status'] === 'late' ? 'Vencida' : 'Pendiente' }}
                                        </span>
                                    </label>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @else
                    <div class="p-4 bg-blue-50 border border-blue-100 rounded-2xl mb-4">
                        <p class="text-[10px] font-black uppercase tracking-widest text-blue-600 mb-1">Préstamo de Pagos Varios</p>
                        <p class="text-xs text-blue-800 font-medium">Este préstamo no tiene cuotas fijas. Ingrese el monto que desea abonar a continuación.</p>
                    </div>
                    @endif

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-500">Monto Total del Pago</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 font-bold">$</span>
                            <input type="number" id="payment_amount" name="amount" step="0.01" required {{ $loan->type === 'open' ? '' : 'readonly' }} value="0.00" class="w-full pl-8 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-lg font-black text-primary transition-all">
                        </div>
                        <p id="payment-amount-words" class="text-[10px] font-bold text-primary uppercase tracking-tight h-4"></p>
                    </div>
                    
                    @if($loan->type === 'installments')
                    <div id="installment_info" class="p-4 bg-primary/5 rounded-2xl border border-primary/10 hidden">
                        <p class="text-[10px] font-black uppercase tracking-widest text-primary mb-1">Detalle del Pago</p>
                        <p class="text-xs font-bold text-slate-700" id="installment_text">No se han seleccionado cuotas</p>
                    </div>
                    @endif

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-500">Fecha de Pago</label>
                        <input type="date" name="payment_date" required value="{{ date('Y-m-d') }}" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-2xl text-sm focus:ring-2 focus:ring-primary/20 transition-all">
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-500">Método de Pago</label>
                        <select name="payment_method" required class="w-full px-4 py-3 bg-white border border-slate-200 rounded-2xl text-sm focus:ring-2 focus:ring-primary/20 transition-all">
                            <option value="Efectivo">Efectivo</option>
                            <option value="Transferencia">Transferencia</option>
                            <option value="Cheque">Cheque</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-500">Observaciones</label>
                        <textarea name="observations" rows="2" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-2xl text-sm focus:ring-2 focus:ring-primary/20 transition-all" placeholder="Nota opcional..."></textarea>
                    </div>

                    <button type="submit" class="w-full py-4 bg-primary text-white rounded-2xl font-black uppercase text-xs tracking-widest shadow-lg hover:shadow-xl hover:-translate-y-1 transition-all flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-[20px]">check_circle</span>
                        Procesar Pago
                                </button>
                </form>
            </x-ui.card>

            <!-- Balance Status Card -->
            <div class="bg-primary text-white rounded-[2.5rem] p-8 border-none shadow-lg shadow-primary/20 overflow-hidden relative mb-6">
                <div class="relative z-10 text-white">
                    <h3 class="text-xs font-black uppercase tracking-widest text-white/40 mb-6">Estado de Balance</h3>
                    <div class="space-y-6">
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-widest text-white/40 mb-1">Balance Pendiente</p>
                            <p class="text-4xl font-black text-white">${{ number_format($loan->balance, 2) }}</p>
                        </div>
                        <div class="flex justify-between items-center text-sm pt-4 border-t border-white/10 uppercase tracking-widest font-black">
                            <span class="text-white/40">Interés Base</span>
                            <span class="text-white">${{ number_format($loan->calculateInterests(), 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm uppercase tracking-widest font-black">
                            <span class="text-white/40">Mora Acumulada</span>
                            <span class="text-red-400">${{ number_format($loan->calculateLateFees(), 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm uppercase tracking-widest font-black">
                            <span class="text-white/40">Total Pagado</span>
                            <span class="text-green-300">${{ number_format($loan->payments->sum('amount'), 2) }}</span>
                        </div>
                    </div>
                </div>
                <div class="absolute -right-10 -top-10 w-32 h-32 bg-white/10 rounded-full blur-3xl"></div>
            </div>

            <x-ui.card>
                <h4 class="text-xs font-black uppercase tracking-widest text-slate-500 mb-4">Información del Crédito</h4>
                <div class="space-y-4">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-slate-500 font-medium">Fecha Inicio</span>
                        <span class="text-slate-900 font-bold">{{ $loan->start_date?->format('d/m/Y') ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-slate-500 font-medium">Fecha Vence</span>
                        <span class="text-slate-900 font-bold">{{ $loan->estimated_end_date ? $loan->estimated_end_date->format('d/m/Y') : 'Abierto' }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-slate-500 font-medium">Cuotas</span>
                        <span class="text-slate-900 font-bold">{{ $loan->type === 'open' ? 'Abierto' : $loan->installments . ' cuotas' }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-slate-500 font-medium">Monto Cuota</span>
                        <span class="text-primary font-black">{{ $loan->type === 'open' ? 'N/A' : '$' . number_format((float) $loan->installment_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-slate-500 font-medium">Modalidad</span>
                        <span class="text-primary font-black uppercase tracking-tighter text-xs">{{ $loan->payment_modality }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-slate-500 font-medium">Recargo Mora</span>
                        <span class="text-red-600 font-black tracking-tighter text-xs">{{ number_format($loan->late_fee_percentage, 1) }}%</span>
                    </div>
                </div>
            </x-ui.card>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const installmentCheckboxes = document.querySelectorAll('.installment-checkbox');
            const paymentAmountInput = document.getElementById('payment_amount');
            const installmentText = document.getElementById('installment_text');
            const installmentInfo = document.getElementById('installment_info');
            const observationsTextarea = document.querySelector('textarea[name="observations"]');

            function updatePaymentDetails() {
                let total = 0;
                let selectedNumbers = [];
                
                installmentCheckboxes.forEach(cb => {
                    if (cb.checked) {
                        total += parseFloat(cb.dataset.amount);
                        selectedNumbers.push(cb.value);
                    }
                });

                paymentAmountInput.value = total.toFixed(2);
                
                // Update words
                const wordsDisplay = document.getElementById('payment-amount-words');
                if (wordsDisplay) {
                    wordsDisplay.innerText = total > 0 ? window.numeroALetras.convert(total) : '';
                }
                
                if (selectedNumbers.length > 0) {
                    installmentInfo.classList.remove('hidden');
                    const sorted = [...selectedNumbers].sort((a, b) => a - b);
                    installmentText.innerText = `Pagando Cuota(s): #${sorted.join(', #')}`;
                    observationsTextarea.value = `Pago de cuota(s) #${sorted.join(', #')}`;
                } else {
                    installmentInfo.classList.add('hidden');
                    installmentText.innerText = 'No se han seleccionado cuotas';
                    observationsTextarea.value = '';
                }
            }

            const paymentForm = document.querySelector('form[action="{{ route('payments.store') }}"]');
            if (paymentForm) {
                paymentForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const amount = paymentAmountInput.value;
                    const details = installmentText ? installmentText.innerText : 'Abono a capital/interés';
                    const method = document.querySelector('select[name="payment_method"]').value;

                    if (parseFloat(amount) <= 0) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Monto inválido',
                            text: 'Por favor seleccione al menos una cuota para pagar.',
                            confirmButtonColor: '#0f172a',
                        });
                        return;
                    }

                    Swal.fire({
                        title: 'Confirmar Cobro',
                        html: `
                            <div class="text-left bg-slate-50 p-4 rounded-2xl border border-slate-100 space-y-2">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Resumen del Pago</p>
                                <div class="flex justify-between py-1 border-b border-slate-200/50">
                                    <span class="text-sm text-slate-600">Cliente:</span>
                                    <span class="text-sm font-black text-slate-900 uppercase">{{ $loan->customer->name }}</span>
                                </div>
                                <div class="flex justify-between py-1 border-b border-slate-200/50">
                                    <span class="text-sm text-slate-600">Cuotas:</span>
                                    <span class="text-sm font-black text-primary">${details.replace('Pagando ', '')}</span>
                                </div>
                                <div class="flex justify-between py-1 border-b border-slate-200/50">
                                    <span class="text-sm text-slate-600">Método:</span>
                                    <span class="text-sm font-black text-slate-900">${method}</span>
                                </div>
                                <div class="flex justify-between pt-2">
                                    <span class="text-base font-bold text-slate-900">Total a Recibir:</span>
                                    <span class="text-xl font-black text-primary">$${amount}</span>
                                </div>
                                <p class="text-[10px] font-bold text-slate-500 uppercase mt-2">${window.numeroALetras.convert(amount)}</p>
                            </div>
                        `,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, Registrar Pago',
                        cancelButtonText: 'Cancelar',
                        confirmButtonColor: '#0f172a',
                        cancelButtonColor: '#f1f5f9',
                        customClass: {
                            cancelButton: '!text-slate-600 !font-bold'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.submit();
                        }
                    });
                });
            }

            if (paymentAmountInput) {
                paymentAmountInput.addEventListener('input', () => {
                    const total = parseFloat(paymentAmountInput.value) || 0;
                    const wordsDisplay = document.getElementById('payment-amount-words');
                    if (wordsDisplay) {
                        wordsDisplay.innerText = total > 0 ? window.numeroALetras.convert(total) : '';
                    }
                });
            }

            installmentCheckboxes.forEach(cb => {
                cb.addEventListener('change', updatePaymentDetails);
            });
        });

        function settleLoan() {
            Swal.fire({
                title: 'Liquidación Especial',
                text: 'Indique el monto acordado para saldar este préstamo por completo:',
                input: 'number',
                inputAttributes: {
                    min: 0,
                    step: 0.01,
                    placeholder: 'Monto de liquidación...'
                },
                showCancelButton: true,
                confirmButtonText: 'Saldar Ahora',
                confirmButtonColor: '#f59e0b',
                cancelButtonText: 'Cancelar',
                showLoaderOnConfirm: true,
                preConfirm: (amount) => {
                    if (!amount || amount < 0) {
                        Swal.showValidationMessage('Por favor ingrese un monto válido');
                        return false;
                    }
                    return fetch(`{{ route('loans.settle', $loan) }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ amount: amount })
                    }).then(response => {
                        if (!response.ok) {
                            throw new Error('Error al procesar la liquidación');
                        }
                        window.location.reload();
                    }).catch(error => {
                        Swal.showValidationMessage(`Error: ${error.message}`);
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
            });
        }
    </script>
</x-layouts.app>
