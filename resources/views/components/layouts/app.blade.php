<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Sys_prestamos') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;900&family=Outfit:wght@400;500;900&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/imask"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-slate-50 font-sans antialiased">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <x-layout.sidebar>
            <div class="px-6 py-10 mb-6">
                <h1 class="text-[1.6rem] font-black text-primary tracking-tight uppercase italic">Sys<span class="text-slate-900">Prestamos</span></h1>
            </div>

            <nav class="space-y-2 px-3">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-4 px-5 py-4 rounded-2xl transition-all duration-300 {{ request()->routeIs('dashboard') ? 'bg-primary text-white shadow-lg' : 'text-slate-600 hover:bg-primary hover:text-white hover:shadow-md' }}">
                    <span class="material-symbols-outlined text-2xl">dashboard</span>
                    <span class="font-bold text-base">Dashboard</span>
                </a>

                <a href="{{ route('customers.index') }}" class="flex items-center gap-4 px-5 py-4 rounded-2xl transition-all duration-300 {{ request()->routeIs('customers*') ? 'bg-primary text-white shadow-lg' : 'text-slate-600 hover:bg-primary hover:text-white hover:shadow-md' }}">
                    <span class="material-symbols-outlined text-2xl">group</span>
                    <span class="font-bold text-base">Clientes</span>
                </a>

                <a href="{{ route('loans.index') }}" class="flex items-center gap-4 px-5 py-4 rounded-2xl transition-all duration-300 {{ request()->routeIs('loans*') ? 'bg-primary text-white shadow-lg' : 'text-slate-600 hover:bg-primary hover:text-white hover:shadow-md' }}">
                    <span class="material-symbols-outlined text-2xl">real_estate_agent</span>
                    <span class="font-bold text-base">Préstamos</span>
                </a>

                <a href="{{ route('payments.index') }}" class="flex items-center gap-4 px-5 py-4 rounded-2xl transition-all duration-300 {{ request()->routeIs('payments*') ? 'bg-primary text-white shadow-lg' : 'text-slate-600 hover:bg-primary hover:text-white hover:shadow-md' }}">
                    <span class="material-symbols-outlined text-2xl">payments</span>
                    <span class="font-bold text-base">Pagos</span>
                </a>

                <a href="{{ route('calculator.index') }}" class="flex items-center gap-4 px-5 py-4 rounded-2xl transition-all duration-300 {{ request()->routeIs('calculator*') ? 'bg-primary text-white shadow-lg' : 'text-slate-600 hover:bg-primary hover:text-white hover:shadow-md' }}">
                    <span class="material-symbols-outlined text-2xl">calculate</span>
                    <span class="font-bold text-base">Calculadora</span>
                </a>

                <a href="{{ route('reports.index') }}" class="flex items-center gap-4 px-5 py-4 rounded-2xl transition-all duration-300 {{ request()->routeIs('reports*') ? 'bg-primary text-white shadow-lg' : 'text-slate-600 hover:bg-primary hover:text-white hover:shadow-md' }}">
                    <span class="material-symbols-outlined text-2xl">analytics</span>
                    <span class="font-bold text-base">Reportes</span>
                </a>
            </nav>
        </x-layout.sidebar>

        <!-- Main Content -->
        <main class="flex-1 lg:pl-64 flex flex-col min-h-screen">
            <!-- Topbar -->
            <x-layout.topbar>
                <x-slot name="search">
                    <x-layout.search-palette />
                </x-slot>

                <div class="flex items-center gap-4">
                    <button class="w-10 h-10 rounded-xl bg-white border border-slate-100 flex items-center justify-center text-slate-500 hover:text-primary transition-colors">
                        <span class="material-symbols-outlined">notifications</span>
                    </button>
                    <div class="h-8 w-[1px] bg-slate-200"></div>
                    <div class="flex items-center gap-3">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-bold text-slate-900 leading-none">Admin</p>
                            <p class="text-[10px] font-medium text-slate-500 uppercase tracking-widest mt-1">Gestor</p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-primary/10 border-2 border-primary/20 flex items-center justify-center text-primary font-bold">
                            A
                        </div>
                    </div>
                </div>
            </x-layout.topbar>

            <!-- Page Content -->
            <div class="p-6 md:p-10 max-w-[1700px] mx-auto w-full flex-1 space-y-6">
                {{ $slot }}
            </div>

            <!-- Footer -->
            <footer class="p-6 border-t border-slate-100 text-center">
                <p class="text-xs text-slate-500 font-medium tracking-widest uppercase">© 2026 Enterprise Financial Core — Sys_prestamos</p>
            </footer>
        </main>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // IMask initialization
            document.querySelectorAll('[data-mask="identification"]').forEach(input => {
                IMask(input, { mask: '000-0000000-0' });
            });
            document.querySelectorAll('[data-mask="phone"]').forEach(input => {
                IMask(input, { mask: '000-000-0000' });
            });

            // SweetAlert2 Session Messages
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            @if(session('success'))
                Toast.fire({ icon: 'success', title: "{{ session('success') }}" });
            @endif

            @if(session('error'))
                Swal.fire({ icon: 'error', title: '¡Error!', text: "{{ session('error') }}", confirmButtonColor: '#004a99' });
            @endif

            @if(session('warning'))
                Swal.fire({ icon: 'warning', title: 'Atención', text: "{{ session('warning') }}", confirmButtonColor: '#004a99' });
            @endif

            // --- Anti-Error Utilities ---
            
            // 1. Double-click protection
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', (e) => {
                    const submitBtn = form.querySelector('button[type="submit"]');
                    if (submitBtn && form.checkValidity()) {
                        setTimeout(() => {
                            submitBtn.disabled = true;
                            submitBtn.innerHTML = '<span class="flex items-center gap-2"><svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> PROCESANDO...</span>';
                        }, 0);
                    }
                });
            });

            // 2. Number to Words (Spanish)
            window.numeroALetras = {
                convert: function(num) {
                    const data = {
                        numero: num,
                        enteros: Math.floor(num),
                        centavos: (((Math.round(num * 100)) - (Math.floor(num) * 100))),
                        letrasCentavos: "",
                        letrasMonedaPlural: 'PESOS',
                        letrasMonedaSingular: 'PESO',
                        letrasMonedaCentavoPlural: 'CENTAVOS',
                        letrasMonedaCentavoSingular: 'CENTAVO'
                    };

                    if (data.centavos > 0) {
                        data.letrasCentavos = "CON " + (function() {
                            if (data.centavos == 1) return "UN " + data.letrasMonedaCentavoSingular;
                            else return data.centavos + "/100";
                        })();
                    } else {
                        data.letrasCentavos = "CON 00/100";
                    }

                    if (data.enteros == 0) return "CERO " + data.letrasMonedaPlural + " " + data.letrasCentavos;
                    if (data.enteros == 1) return this.Seccion(data.enteros, 1, "UN", "UNO") + " " + data.letrasMonedaSingular + " " + data.letrasCentavos;
                    else return this.Seccion(data.enteros, data.enteros, "UN", "UNO") + " " + data.letrasMonedaPlural + " " + data.letrasCentavos;
                },
                Unidades: function(num) {
                    switch (num) {
                        case 1: return "UN";
                        case 2: return "DOS";
                        case 3: return "TRES";
                        case 4: return "CUATRO";
                        case 5: return "CINCO";
                        case 6: return "SEIS";
                        case 7: return "SIETE";
                        case 8: return "OCHO";
                        case 9: return "NUEVE";
                    }
                    return "";
                },
                Decenas: function(num) {
                    let decena = Math.floor(num / 10);
                    let unidad = num - (decena * 10);
                    switch (decena) {
                        case 1:
                            switch (unidad) {
                                case 0: return "DIEZ";
                                case 1: return "ONCE";
                                case 2: return "DOCE";
                                case 3: return "TRECE";
                                case 4: return "CATORCE";
                                case 5: return "QUINCE";
                                default: return "DIECI" + this.Unidades(unidad);
                            }
                        case 2:
                            switch (unidad) {
                                case 0: return "VEINTE";
                                default: return "VEINTI" + this.Unidades(unidad);
                            }
                        case 3: return this.DecenasY("TREINTA", unidad);
                        case 4: return this.DecenasY("CUARENTA", unidad);
                        case 5: return this.DecenasY("CINCUENTA", unidad);
                        case 6: return this.DecenasY("SESENTA", unidad);
                        case 7: return this.DecenasY("SETENTA", unidad);
                        case 8: return this.DecenasY("OCHENTA", unidad);
                        case 9: return this.DecenasY("NOVENTA", unidad);
                        case 0: return this.Unidades(unidad);
                    }
                },
                DecenasY: function(strSin, numUnidades) {
                    if (numUnidades > 0) return strSin + " Y " + this.Unidades(numUnidades);
                    return strSin;
                },
                Centenas: function(num) {
                    let centenas = Math.floor(num / 100);
                    let decenas = num - (centenas * 100);
                    switch (centenas) {
                        case 1:
                            if (decenas > 0) return "CIENTO " + this.Decenas(decenas);
                            return "CIEN";
                        case 2: return "DOSCIENTOS " + this.Decenas(decenas);
                        case 3: return "TRESCIENTOS " + this.Decenas(decenas);
                        case 4: return "CUATROCIENTOS " + this.Decenas(decenas);
                        case 5: return "QUINIENTOS " + this.Decenas(decenas);
                        case 6: return "SEISCIENTOS " + this.Decenas(decenas);
                        case 7: return "SETECIENTOS " + this.Decenas(decenas);
                        case 8: return "OCHOCIENTOS " + this.Decenas(decenas);
                        case 9: return "NOVECIENTOS " + this.Decenas(decenas);
                    }
                    return this.Decenas(decenas);
                },
                Seccion: function(num, divisor, strSingular, strPlural) {
                    let cientos = Math.floor(num / divisor);
                    let resto = num - (cientos * divisor);
                    let letras = "";
                    if (cientos > 0) {
                        if (cientos > 1) letras = this.Centenas(cientos) + " " + strPlural;
                        else letras = strSingular;
                    }
                    if (resto > 0) letras += (letras !== "" ? " " : "") + this.Centenas(resto);
                    return letras;
                }
            };
        });
    </script>
</body>
</html>
