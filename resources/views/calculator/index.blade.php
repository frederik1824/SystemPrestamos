<x-layouts.app>
    <div class="mb-10">
        <h2 class="text-4xl font-black text-slate-900 tracking-tighter uppercase mb-2">Simulador de Préstamos</h2>
        <p class="text-slate-500 font-medium italic">Calcule sus cuotas y planifique su crédito de forma inmediata.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
        <!-- Input Panel -->
        <x-ui.card class="border-2 border-primary/5 shadow-xl shadow-primary/5">
            <h3 class="text-xl font-black text-slate-900 tracking-tight mb-8 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">calculate</span>
                Parámetros del Crédito
            </h3>
            
            <div class="space-y-8">
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-500">Monto del Préstamo</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 font-bold">$</span>
                        <input type="number" id="calc-amount" value="10000" class="w-full pl-8 pr-4 py-4 bg-slate-50 border-none rounded-2xl text-lg font-black text-slate-900 focus:ring-2 focus:ring-primary/20 transition-all outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-500">Cuotas</label>
                        <input type="number" id="calc-installments" value="12" min="1" class="w-full px-4 py-4 bg-slate-50 border-none rounded-2xl text-lg font-black text-slate-900 focus:ring-2 focus:ring-primary/20 transition-all outline-none">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-500">Interés (%)</label>
                        <input type="number" id="calc-rate" value="5" step="0.1" class="w-full px-4 py-4 bg-slate-50 border-none rounded-2xl text-lg font-black text-slate-900 focus:ring-2 focus:ring-primary/20 transition-all outline-none">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-500">Tipo de Interés</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="relative cursor-pointer group">
                            <input type="radio" name="calc-type" value="simple" checked class="hidden peer">
                            <div class="p-4 rounded-2xl border-2 border-slate-100 bg-white text-center transition-all peer-checked:border-primary peer-checked:bg-primary/5 group-hover:bg-slate-50">
                                <p class="text-[10px] font-black uppercase tracking-widest text-slate-500 peer-checked:text-primary">Fijo / Directo</p>
                                <p class="font-bold text-slate-900">Simple</p>
                            </div>
                        </label>
                        <label class="relative cursor-pointer group">
                            <input type="radio" name="calc-type" value="compound" class="hidden peer">
                            <div class="p-4 rounded-2xl border-2 border-slate-100 bg-white text-center transition-all peer-checked:border-primary peer-checked:bg-primary/5 group-hover:bg-slate-50">
                                <p class="text-[10px] font-black uppercase tracking-widest text-slate-500 peer-checked:text-primary">Amortizado</p>
                                <p class="font-bold text-slate-900">Francés</p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
        </x-ui.card>

        <!-- Result Panel -->
        <div class="space-y-6">
            <x-ui.card class="!bg-primary text-white border-none relative overflow-hidden h-full flex flex-col justify-center p-10">
                <div class="relative z-10 text-center">
                    <p class="text-[10px] font-black uppercase tracking-widest text-white/50 mb-4 italic">Cuota Estimada</p>
                    <h4 class="text-6xl font-black text-white mb-10" id="res-installment">$0.00</h4>
                    
                    <div class="grid grid-cols-2 gap-8 pt-10 border-t border-white/10">
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-widest text-white/60 mb-2">Total con Interés</p>
                            <p class="text-2xl font-black text-white" id="res-total">$0.00</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-widest text-white/60 mb-2">Interés Generado</p>
                            <p class="text-2xl font-black text-green-300" id="res-interest">$0.00</p>
                        </div>
                    </div>
                </div>
                
                <!-- Background decoration -->
                <div class="absolute -right-20 -bottom-20 w-96 h-96 bg-white/5 rounded-full blur-3xl"></div>
                <div class="absolute -left-10 -top-10 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
            </x-ui.card>

            <div class="grid grid-cols-3 gap-4">
                <div class="bg-white p-4 rounded-2xl border border-slate-100 text-center">
                    <span class="material-symbols-outlined text-primary mb-2">history_edu</span>
                    <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Sin Papeleo</p>
                </div>
                <div class="bg-white p-4 rounded-2xl border border-slate-100 text-center">
                    <span class="material-symbols-outlined text-primary mb-2">speed</span>
                    <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Instantáneo</p>
                </div>
                <div class="bg-white p-4 rounded-2xl border border-slate-100 text-center">
                    <span class="material-symbols-outlined text-primary mb-2">verified</span>
                    <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Preciso</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Live Amortization Table -->
    <div class="mt-10 print-section">
        <x-ui.card>
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-slate-900 tracking-tight">Tabla de Amortización Estimada</h3>
                <button onclick="window.print()" class="flex items-center gap-2 px-4 py-2 bg-slate-100 text-slate-600 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-primary hover:text-white transition-all no-print">
                    <span class="material-symbols-outlined text-sm">print</span>
                    Imprimir Tabla
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-slate-500 text-[10px] font-black uppercase tracking-widest border-b border-slate-50">
                            <th class="pb-4">Cuota</th>
                            <th class="pb-4">Balance Inicial</th>
                            <th class="pb-4">Principal</th>
                            <th class="pb-4">Interés</th>
                            <th class="pb-4">Total</th>
                            <th class="pb-4 text-right">Balance Final</th>
                        </tr>
                    </thead>
                    <tbody id="amortization-body" class="divide-y divide-slate-50 text-xs font-medium text-slate-600">
                        <!-- Filled by JS -->
                    </tbody>
                </table>
            </div>
        </x-ui.card>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const amountInput = document.getElementById('calc-amount');
            const installmentsInput = document.getElementById('calc-installments');
            const rateInput = document.getElementById('calc-rate');
            const typeRadios = document.getElementsByName('calc-type');
            
            const displayInstallment = document.getElementById('res-installment');
            const displayTotal = document.getElementById('res-total');
            const displayInterest = document.getElementById('res-interest');
            const amortizationBody = document.getElementById('amortization-body');

            function getFormat(num) {
                return '$' + num.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            }

            function update() {
                const P = parseFloat(amountInput.value) || 0;
                const n = parseInt(installmentsInput.value) || 1;
                const r = (parseFloat(rateInput.value) || 0) / 100;
                let type = 'simple';
                typeRadios.forEach(r => { if(r.checked) type = r.value; });

                let installment = 0;
                let totalInterest = 0;
                let totalPayable = 0;

                amortizationBody.innerHTML = '';

                if (type === 'simple') {
                    totalInterest = P * r;
                    totalPayable = P + totalInterest;
                    installment = totalPayable / n;

                    let balance = totalPayable;
                    for (let i = 1; i <= n; i++) {
                        const initBalance = balance;
                        balance -= installment;
                        
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td class="py-3 font-bold text-slate-900">${i}</td>
                            <td class="py-3">${getFormat(initBalance)}</td>
                            <td class="py-3">${getFormat(P/n)}</td>
                            <td class="py-3">${getFormat(totalInterest/n)}</td>
                            <td class="py-3 font-bold text-primary">${getFormat(installment)}</td>
                            <td class="py-3 text-right">${getFormat(Math.max(0, balance))}</td>
                        `;
                        amortizationBody.appendChild(row);
                    }
                } else {
                    if (r === 0) {
                        installment = P / n;
                    } else {
                        const factor = Math.pow(1 + r, n);
                        installment = (P * (r * factor)) / (factor - 1);
                    }
                    totalPayable = installment * n;
                    totalInterest = totalPayable - P;

                    let balance = P;
                    for (let i = 1; i <= n; i++) {
                        const iPart = balance * r;
                        const pPart = installment - iPart;
                        const initBalance = balance;
                        balance -= pPart;

                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td class="py-3 font-bold text-slate-900">${i}</td>
                            <td class="py-3">${getFormat(initBalance)}</td>
                            <td class="py-3">${getFormat(pPart)}</td>
                            <td class="py-3">${getFormat(iPart)}</td>
                            <td class="py-3 font-bold text-primary">${getFormat(installment)}</td>
                            <td class="py-3 text-right">${getFormat(Math.max(0, balance))}</td>
                        `;
                        amortizationBody.appendChild(row);
                    }
                }

                displayInstallment.innerText = getFormat(installment);
                displayTotal.innerText = getFormat(totalPayable);
                displayInterest.innerText = getFormat(totalInterest);
            }

            [amountInput, installmentsInput, rateInput].forEach(input => {
                input.addEventListener('input', update);
            });
            typeRadios.forEach(radio => radio.addEventListener('change', update));

            update();
        });
    </script>
</x-layouts.app>
