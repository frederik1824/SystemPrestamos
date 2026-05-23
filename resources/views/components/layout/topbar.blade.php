<header class="sticky top-0 z-40 bg-white/80 backdrop-blur-md h-16 px-4 md:px-8 flex justify-between items-center shadow-sm">
    <div class="flex items-center gap-4 md:gap-8">
        <!-- Mobile Menu Toggle -->
        <button @click="sidebarOpen = true" class="lg:hidden p-2 -ml-2 rounded-xl text-slate-500 hover:bg-slate-100 transition-colors">
            <span class="material-symbols-outlined">menu</span>
        </button>
        {{ $search ?? '' }}
        <nav class="flex gap-6">
            {{ $nav ?? '' }}
        </nav>
    </div>
    
    <div class="flex items-center gap-4">
        {{ $slot }}
    </div>
</header>
