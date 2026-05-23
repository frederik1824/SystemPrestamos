<x-layouts.app>
    <div class="mb-10 flex items-center gap-4">
        <a href="{{ route('customers.index') }}" class="w-10 h-10 rounded-xl bg-white border border-slate-100 flex items-center justify-center text-slate-500 hover:text-primary transition-colors">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <div>
            <h2 class="text-4xl font-black text-slate-900 tracking-tighter uppercase mb-1">Registrar Cliente</h2>
            <p class="text-slate-500 font-medium text-sm italic">Complete los campos para agregar un nuevo cliente al sistema.</p>
        </div>
    </div>

    <div class="max-w-4xl mx-auto">
        <x-ui.card>
            <form action="{{ route('customers.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-10 flex flex-col items-center">
                    <div class="relative group cursor-pointer" onclick="document.getElementById('photo-input').click()">
                        <div class="w-32 h-32 rounded-[2.5rem] bg-slate-50 border-2 border-dashed border-slate-200 flex flex-col items-center justify-center text-slate-400 group-hover:border-primary/50 group-hover:text-primary transition-all overflow-hidden relative">
                            <span class="material-symbols-outlined text-4xl mb-1" id="photo-icon">add_a_photo</span>
                            <span class="text-[9px] font-black uppercase tracking-widest text-center px-4" id="photo-text">Subir Foto</span>
                            <img id="photo-preview" class="absolute inset-0 w-full h-full object-cover hidden">
                        </div>
                        <div class="absolute -right-2 -bottom-2 w-10 h-10 bg-primary text-white rounded-xl shadow-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined text-xl">upload</span>
                        </div>
                    </div>
                    <input type="file" name="photo" id="photo-input" class="hidden" accept="image/*" onchange="previewImage(this)">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-4">Foto de Perfil (Opcional)</p>
                    @error('photo') <p class="text-[10px] text-rose-500 font-bold uppercase mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-500">Nombre Completo</label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-3 bg-slate-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-primary/20 transition-all" placeholder="Ej. Juan Pérez">
                        @error('name') <p class="text-[10px] text-rose-500 font-bold uppercase mt-1 px-2">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-500">Cédula / ID</label>
                        <input type="text" name="identification_id" value="{{ old('identification_id') }}" data-mask="identification" required class="w-full px-4 py-3 bg-slate-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-primary/20 transition-all" placeholder="000-0000000-0">
                        @error('identification_id') <p class="text-[10px] text-rose-500 font-bold uppercase mt-1 px-2">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-500">Teléfono</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" data-mask="phone" required class="w-full px-4 py-3 bg-slate-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-primary/20 transition-all" placeholder="000-000-0000">
                        @error('phone') <p class="text-[10px] text-rose-500 font-bold uppercase mt-1 px-2">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-500">Email (Opcional)</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="w-full px-4 py-3 bg-slate-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-primary/20 transition-all" placeholder="cliente@correo.com">
                    </div>

                    <div class="md:col-span-2 space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-500">Dirección</label>
                        <textarea name="address" rows="3" class="w-full px-4 py-3 bg-slate-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-primary/20 transition-all" placeholder="Calle, Sector, Ciudad...">{{ old('address') }}</textarea>
                    </div>

                    <div class="md:col-span-2 space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-500">Garantía / Aval</label>
                        <textarea name="guarantee" rows="2" class="w-full px-4 py-3 bg-slate-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-primary/20 transition-all" placeholder="Especifique si deja alguna garantía...">{{ old('guarantee') }}</textarea>
                        @error('guarantee') <p class="text-[10px] text-rose-500 font-bold uppercase mt-1 px-2">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2 space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-500">Notas / Comentarios</label>
                        <textarea name="notes" rows="2" class="w-full px-4 py-3 bg-slate-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-primary/20 transition-all" placeholder="Observaciones adicionales...">{{ old('notes') }}</textarea>
                        @error('notes') <p class="text-[10px] text-rose-500 font-bold uppercase mt-1 px-2">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
                    <a href="{{ route('customers.index') }}" class="px-6 py-3 text-slate-500 font-bold uppercase text-[10px] tracking-widest hover:text-slate-900 transition-colors">Cancelar</a>
                    <button type="submit" data-confirm="¿Estás seguro de que deseas registrar este nuevo cliente?" data-confirm-type="info" class="px-8 py-4 bg-primary text-white rounded-2xl font-bold uppercase text-xs tracking-widest shadow-lg hover:shadow-xl hover:-translate-y-1 transition-all">
                        Guardar Cliente
                    </button>
                </div>
            </form>
        </x-ui.card>
    </div>
    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('photo-preview').src = e.target.result;
                    document.getElementById('photo-preview').classList.remove('hidden');
                    document.getElementById('photo-icon').classList.add('hidden');
                    document.getElementById('photo-text').classList.add('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</x-layouts.app>
