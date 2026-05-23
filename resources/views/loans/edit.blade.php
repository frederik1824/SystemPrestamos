<x-layouts.app>
    <div class="mb-10 flex items-center gap-4">
        <a href="{{ route('loans.show', $loan) }}" class="w-10 h-10 rounded-xl bg-white border border-slate-100 flex items-center justify-center text-slate-500 hover:text-primary transition-colors">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <div>
            <h2 class="text-4xl font-black text-slate-900 tracking-tighter uppercase mb-1">Editar Préstamo</h2>
            <p class="text-slate-500 font-medium text-sm italic">Modificar condiciones del crédito #{{ $loan->id }}.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <x-ui.card>
                <form action="{{ route('loans.update', $loan) }}" method="POST" id="loan-form">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                        <div class="md:col-span-2 space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-500">Tipo de Préstamo</label>
                            <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl">
                                <p class="text-sm font-bold text-slate-900 uppercase">
                                    {{ $loan->type === 'installments' ? 'Amortización (Cuotas)' : 'Pagos Varios (Abierto)' }}
                                </p>
                                <p class="text-[10px] text-slate-500 font-medium italic mt-1">* El tipo de préstamo y el cliente no pueden ser modificados una vez creados.</p>
                                <input type="hidden" name="type" value="{{ $loan->type }}">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label for="amount" class="text-[10px] font-black uppercase tracking-widest text-slate-500">Monto del Préstamo</label>
                            <input type="number" name="amount" id="amount" step="0.01" required value="{{ old('amount', $loan->amount) }}" class="w-full px-4 py-4 bg-white border border-slate-200 rounded-2xl text-lg font-black focus:ring-4 focus:ring-primary/10 transition-all font-mono">
                            <p id="amount-words" class="text-[10px] font-bold text-primary uppercase tracking-tight h-4"></p>
                        </div>

                        <div class="space-y-2">
                            <label for="interest_rate" class="text-[10px] font-black uppercase tracking-widest text-slate-500">Tasa de Interés (%)</label>
                            <input type="number" name="interest_rate" id="interest_rate" step="0.01" required value="{{ old('interest_rate', $loan->interest_rate) }}" class="w-full px-4 py-4 bg-white border border-slate-200 rounded-2xl text-lg font-black focus:ring-4 focus:ring-primary/10 transition-all">
                        </div>

                        <div class="space-y-2">
                            <label for="late_fee_percentage" class="text-[10px] font-black uppercase tracking-widest text-slate-500">Recargo por Mora (%)</label>
                            <input type="number" name="late_fee_percentage" id="late_fee_percentage" step="0.01" value="{{ old('late_fee_percentage', $loan->late_fee_percentage) }}" class="w-full px-4 py-4 bg-white border border-slate-200 rounded-2xl text-lg font-black focus:ring-4 focus:ring-primary/10 transition-all font-mono">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:col-span-2 {{ $loan->type === 'open' ? 'hidden' : '' }}" id="installment-fields">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black uppercase tracking-widest text-slate-500">Cantidad de Cuotas</label>
                                <input type="number" name="installments" id="installments" value="{{ old('installments', $loan->installments) }}" {{ $loan->type === 'installments' ? 'required' : '' }} min="1" class="w-full px-4 py-3 bg-slate-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-primary/20 transition-all">
                            </div>

                            <div class="space-y-2">
                                <label class="text-[10px] font-black uppercase tracking-widest text-slate-500">Tipo de Interés</label>
                                <select name="interest_type" id="interest_type" class="w-full px-4 py-3 bg-slate-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-primary/20 transition-all">
                                    <option value="simple" {{ $loan->interest_type === 'simple' ? 'selected' : '' }}>Simple (Fijo)</option>
                                    <option value="compound" {{ $loan->interest_type === 'compound' ? 'selected' : '' }}>Amortizado (Francés)</option>
                                </select>
                            </div>

                            <div class="space-y-2">
                                <label class="text-[10px] font-black uppercase tracking-widest text-slate-500">Modalidad de Pago</label>
                                <select name="payment_modality" id="payment_modality" class="w-full px-4 py-3 bg-slate-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-primary/20 transition-all">
                                    <option value="mensual" {{ $loan->payment_modality === 'mensual' ? 'selected' : '' }}>Mensual</option>
                                    <option value="quincenal" {{ $loan->payment_modality === 'quincenal' ? 'selected' : '' }}>Quincenal</option>
                                    <option value="semanal" {{ $loan->payment_modality === 'semanal' ? 'selected' : '' }}>Semanal</option>
                                    <option value="diario" {{ $loan->payment_modality === 'diario' ? 'selected' : '' }}>Diario</option>
                                </select>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label for="start_date" class="text-[10px] font-black uppercase tracking-widest text-slate-500">Fecha de Inicio</label>
                            <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $loan->start_date->format('Y-m-d')) }}" required class="w-full px-4 py-4 bg-white border border-slate-200 rounded-2xl text-sm focus:ring-4 focus:ring-primary/10 transition-all">
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
                        <a href="{{ route('loans.show', $loan) }}" class="px-6 py-3 text-slate-500 font-bold uppercase text-[10px] tracking-widest hover:text-slate-900 transition-colors">Cancelar</a>
                        <button type="submit" class="px-8 py-4 bg-primary text-white rounded-2xl font-bold uppercase text-xs tracking-widest shadow-lg hover:shadow-xl hover:-translate-y-1 transition-all">
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </x-ui.card>
        </div>

        <div class="space-y-6">
            <div class="bg-slate-900 text-white rounded-[2.5rem] p-8 border-none shadow-sm overflow-hidden relative">
                <div class="relative z-10">
                    <h3 class="text-xs font-black uppercase tracking-widest text-slate-400 mb-6">Resumen de Cuota Proyectada</h3>
                    <div class="space-y-6">
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1" id="preview-installment-label">Monto por Cuota</p>
                            <p class="text-4xl font-black text-primary" id="preview-installment">$0.00</p>
                        </div>
                        <div class="grid grid-cols-2 gap-4 pt-6 border-t border-white/10">
                            <div>
                                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1" id="preview-total-label">Total a Pagar</p>
                                <p class="text-lg font-bold text-white uppercase tracking-tight" id="preview-total">$0.00</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Interés Total</p>
                                <p class="text-lg font-bold text-green-400 uppercase tracking-tight" id="preview-interest">$0.00</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="absolute -right-10 -top-10 w-32 h-32 bg-primary/20 rounded-full blur-3xl"></div>
            </div>

            <x-ui.card>
                <h3 class="text-xs font-black uppercase tracking-widest text-amber-500 mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">warning</span>
                    Advertencia
                </h3>
                <p class="text-xs text-slate-600 leading-relaxed font-medium">
                    Modificar las condiciones de un préstamo regenerará su tabla de amortización. Esto solo está permitido para préstamos que aún no tienen pagos registrados.
                </p>
            </x-ui.card>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const amountInput = document.getElementById('amount');
            const rateInput = document.getElementById('interest_rate');
            const installmentsInput = document.getElementById('installments');
            const typeInput = document.getElementById('interest_type');
            const loanType = document.querySelector('input[name="type"]').value;
            
            const displayInstallment = document.getElementById('preview-installment');
            const displayTotal = document.getElementById('preview-total');
            const displayInterest = document.getElementById('preview-interest');

            function calculate() {
                const P = parseFloat(amountInput.value) || 0;
                const r = (parseFloat(rateInput.value) || 0) / 100;
                
                let installment = 0;
                let total = 0;
                let interest = 0;

                if (loanType === 'open') {
                    interest = P * r;
                    total = P + interest;
                    
                    document.getElementById('preview-installment-label').innerText = 'Interés Mensual Inicial';
                    displayInstallment.innerText = '$' + interest.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                    document.getElementById('preview-total-label').innerText = 'Deuda Proyectada Mes 1';
                } else {
                    const n = parseInt(installmentsInput.value) || 1;
                    const type = typeInput.value;
                    
                    document.getElementById('preview-installment-label').innerText = 'Monto por Cuota';
                    document.getElementById('preview-total-label').innerText = 'Total a Pagar';

                    if (type === 'simple') {
                        interest = P * r;
                        total = P + interest;
                        installment = total / n;
                    } else {
                        if (r === 0) {
                            installment = P / n;
                        } else {
                            const factor = Math.pow(1 + r, n);
                            installment = (P * (r * factor)) / (factor - 1);
                        }
                        total = installment * n;
                        interest = total - P;
                    }
                    displayInstallment.innerText = '$' + installment.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                }

                displayTotal.innerText = '$' + total.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                displayInterest.innerText = '$' + interest.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            }

            [amountInput, installmentsInput, rateInput, typeInput].forEach(input => {
                if(input) {
                    input.addEventListener('input', () => {
                        calculate();
                        if (input === amountInput && window.numeroALetras) {
                            const val = parseFloat(amountInput.value) || 0;
                            document.getElementById('amount-words').innerText = val > 0 ? window.numeroALetras.convert(val) : '';
                        }
                    });
                }
            });

            // Initial calculation
            calculate();
            if (window.numeroALetras) {
                const val = parseFloat(amountInput.value) || 0;
                document.getElementById('amount-words').innerText = val > 0 ? window.numeroALetras.convert(val) : '';
            }
        });
    </script>
</x-layouts.app>
