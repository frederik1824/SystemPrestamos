<x-layouts.app>
    <div class="mb-10 flex items-center gap-4">
        <a href="{{ route('loans.index') }}" class="w-10 h-10 rounded-xl bg-white border border-slate-100 flex items-center justify-center text-slate-500 hover:text-primary transition-colors">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <div>
            <h2 class="text-4xl font-black text-slate-900 tracking-tighter uppercase mb-1">Nuevo Préstamo</h2>
            <p class="text-slate-500 font-medium text-sm italic">Configure las condiciones del crédito para el cliente.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <x-ui.card>
                <form action="{{ route('loans.store') }}" method="POST" id="loan-form">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                        <div class="md:col-span-2 space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-500">Tipo de Préstamo</label>
                            <div class="grid grid-cols-2 gap-4">
                                <label class="flex items-center gap-3 p-4 rounded-2xl bg-slate-50 border-2 border-transparent cursor-pointer transition-all hover:bg-slate-100 has-[:checked]:border-primary has-[:checked]:bg-white group">
                                    <input type="radio" name="type" value="installments" checked class="w-5 h-5 text-primary border-slate-300 focus:ring-primary/20 loan-type-radio">
                                    <div>
                                        <p class="text-xs font-black text-slate-900 uppercase">Amortización</p>
                                        <p class="text-[10px] text-slate-500 font-bold uppercase tracking-tighter">Cuotas fijas programadas</p>
                                    </div>
                                </label>
                                <label class="flex items-center gap-3 p-4 rounded-2xl bg-slate-50 border-2 border-transparent cursor-pointer transition-all hover:bg-slate-100 has-[:checked]:border-primary has-[:checked]:bg-white group">
                                    <input type="radio" name="type" value="open" class="w-5 h-5 text-primary border-slate-300 focus:ring-primary/20 loan-type-radio">
                                    <div>
                                        <p class="text-xs font-black text-slate-900 uppercase">Pagos Varios</p>
                                        <p class="text-[10px] text-slate-500 font-bold uppercase tracking-tighter">Interés dinámico mensual</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="md:col-span-2 space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-500">Seleccionar Cliente</label>
                            <select name="customer_id" id="customer_id" required class="w-full px-4 py-3 bg-slate-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-primary/20 transition-all">
                                <option value="">-- Seleccione un cliente --</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ (isset($selectedCustomerId) && $selectedCustomerId == $customer->id) ? 'selected' : '' }}>
                                        {{ $customer->name }} ({{ $customer->identification_id }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label for="amount" class="text-[10px] font-black uppercase tracking-widest text-slate-500">Monto del Préstamo</label>
                            <input type="number" name="amount" id="amount" step="0.01" required class="w-full px-4 py-4 bg-white border border-slate-200 rounded-2xl text-lg font-black focus:ring-4 focus:ring-primary/10 transition-all font-mono" placeholder="0.00">
                            <p id="amount-words" class="text-[10px] font-bold text-primary uppercase tracking-tight h-4"></p>
                        </div>

                        <div class="space-y-2">
                            <label for="interest_rate" class="text-[10px] font-black uppercase tracking-widest text-slate-500">Tasa de Interés (%)</label>
                            <input type="number" name="interest_rate" id="interest_rate" step="0.01" required class="w-full px-4 py-4 bg-white border border-slate-200 rounded-2xl text-lg font-black focus:ring-4 focus:ring-primary/10 transition-all" placeholder="0.00">
                            <p id="interest-warning" class="text-[10px] font-bold text-red-500 uppercase tracking-tight hidden">¡Atención! Esta tasa es inusualmente alta.</p>
                        </div>

                        <div class="space-y-2">
                            <label for="late_fee_percentage" class="text-[10px] font-black uppercase tracking-widest text-slate-500">Recargo por Mora (%)</label>
                            <input type="number" name="late_fee_percentage" id="late_fee_percentage" step="0.01" value="5.00" class="w-full px-4 py-4 bg-white border border-slate-200 rounded-2xl text-lg font-black focus:ring-4 focus:ring-primary/10 transition-all font-mono" placeholder="0.00">
                            <p class="text-[9px] text-slate-400 font-bold uppercase mt-1 italic">* Aplicado por cada cuota vencida.</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:col-span-2" id="installment-fields">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black uppercase tracking-widest text-slate-500">Cantidad de Cuotas</label>
                                <input type="number" name="installments" id="installments" required value="{{ old('installments', 1) }}" min="1" class="w-full px-4 py-3 bg-slate-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-primary/20 transition-all">
                            </div>

                            <div class="space-y-2">
                                <label class="text-[10px] font-black uppercase tracking-widest text-slate-500">Tipo de Interés</label>
                                <select name="interest_type" id="interest_type" required class="w-full px-4 py-3 bg-slate-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-primary/20 transition-all">
                                    <option value="simple">Simple (Fijo)</option>
                                    <option value="compound">Amortizado (Francés)</option>
                                </select>
                            </div>

                            <div class="space-y-2">
                                <label class="text-[10px] font-black uppercase tracking-widest text-slate-500">Modalidad de Pago</label>
                                <select name="payment_modality" id="payment_modality" required class="w-full px-4 py-3 bg-slate-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-primary/20 transition-all">
                                    <option value="mensual">Mensual</option>
                                    <option value="quincenal">Quincenal</option>
                                    <option value="semanal">Semanal</option>
                                </select>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label for="start_date" class="text-[10px] font-black uppercase tracking-widest text-slate-500">Fecha de Inicio</label>
                            <input type="date" name="start_date" id="start_date" value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}" required class="w-full px-4 py-4 bg-white border border-slate-200 rounded-2xl text-sm focus:ring-4 focus:ring-primary/10 transition-all">
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
                        <a href="{{ route('loans.index') }}" class="px-6 py-3 text-slate-500 font-bold uppercase text-[10px] tracking-widest hover:text-slate-900 transition-colors">Cancelar</a>
                        <button type="submit" class="px-8 py-4 bg-primary text-white rounded-2xl font-bold uppercase text-xs tracking-widest shadow-lg hover:shadow-xl hover:-translate-y-1 transition-all">
                            Crear Préstamo
                        </button>
                    </div>
                </form>
            </x-ui.card>
        </div>

        <div class="space-y-6">
            <div class="bg-slate-900 text-white rounded-[2.5rem] p-8 border-none shadow-sm overflow-hidden relative">
                <div class="relative z-10">
                    <h3 class="text-xs font-black uppercase tracking-widest text-slate-400 mb-6">Resumen de Cuota</h3>
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
                <h3 class="text-xs font-black uppercase tracking-widest text-slate-500 mb-4">Información</h3>
                <p class="text-xs text-slate-500 leading-relaxed italic">
                    * El cálculo del sistema es automático. La fecha estimada de finalización se calculará según la modalidad de pago y la cantidad de cuotas.
                </p>
            </x-ui.card>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const loanTypeRadios = document.querySelectorAll('.loan-type-radio');
            const installmentFields = document.getElementById('installment-fields');
            const amountInput = document.getElementById('amount');
            const rateInput = document.getElementById('interest_rate');
            const installmentsInput = document.getElementById('installments');
            const typeInput = document.getElementById('interest_type');
            
            const displayInstallment = document.getElementById('preview-installment');
            const displayTotal = document.getElementById('preview-total');
            const displayInterest = document.getElementById('preview-interest');

            function toggleFields() {
                const selectedType = document.querySelector('input[name="type"]:checked').value;
                if (selectedType === 'open') {
                    installmentFields.classList.add('hidden');
                    installmentsInput.required = false;
                    typeInput.required = false;
                    document.getElementById('payment_modality').required = false;
                } else {
                    installmentFields.classList.remove('hidden');
                    installmentsInput.required = true;
                    typeInput.required = true;
                    document.getElementById('payment_modality').required = true;
                }
                calculate();
            }

            function calculate() {
                const selectedType = document.querySelector('input[name="type"]:checked').value;
                const P = parseFloat(amountInput.value) || 0;
                const r = (parseFloat(rateInput.value) || 0) / 100;
                
                let installment = 0;
                let total = 0;
                let interest = 0;

                if (selectedType === 'open') {
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
                input.addEventListener('input', () => {
                    calculate();
                    
                    if (input === amountInput) {
                        const val = parseFloat(amountInput.value) || 0;
                        document.getElementById('amount-words').innerText = val > 0 ? window.numeroALetras.convert(val) : '';
                    }

                    if (input === rateInput) {
                        const rate = parseFloat(rateInput.value) || 0;
                        const warning = document.getElementById('interest-warning');
                        if (rate > 20) {
                            warning.classList.remove('hidden');
                            rateInput.classList.add('border-red-500', 'ring-red-100');
                        } else {
                            warning.classList.add('hidden');
                            rateInput.classList.remove('border-red-500', 'ring-red-100');
                        }
                    }
                });
            });

            const loanForm = document.getElementById('loan-form');
            loanForm.addEventListener('submit', (e) => {
                e.preventDefault();
                const amount = amountInput.value;
                const customer = document.querySelector('select[name="customer_id"] option:checked').text;

                Swal.fire({
                    title: '¿Confirmar Préstamo?',
                    html: `Vas a registrar un préstamo de <b class="text-primary">$${amount}</b> para <b>${customer}</b>.<br><br><span class="text-xs text-slate-500 uppercase font-bold">${window.numeroALetras.convert(amount)}</span>`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#004a99',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Sí, registrar',
                    cancelButtonText: 'Revisar',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        loanForm.submit();
                    }
                });
            });

            loanTypeRadios.forEach(radio => {
                radio.addEventListener('change', toggleFields);
            });

            toggleFields();
        });
    </script>
</x-layouts.app>
