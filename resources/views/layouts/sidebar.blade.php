<nav class="bg-surface text-on-surface-variant font-sans docked fixed-left w-sidebar-width h-full fixed top-0 left-0 z-50 flex flex-col justify-between hidden md:flex transition-all duration-300 ease-in-out border-r border-border-subtle shadow-[4px_0_24px_rgba(0,0,0,0.02)]">
    <div class="w-full flex flex-col h-full">
        <!-- Header -->
        <div class="h-20 flex items-center px-6 mb-2">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary to-indigo-600 flex items-center justify-center mr-3 shadow-md shadow-primary/20 text-white transform transition-transform hover:rotate-12 cursor-pointer">
                <span class="material-symbols-outlined text-[20px]">admin_panel_settings</span>
            </div>
            <div class="flex flex-col justify-center">
                <span class="text-label-lg font-extrabold text-on-surface tracking-tight leading-none">APIP Manager</span>
                <span class="text-[11px] text-on-surface-variant font-medium mt-1">Kab. Puncak Jaya</span>
            </div>
        </div>
        
        <!-- Tab Links -->
        <div class="flex-1 overflow-y-auto px-4 py-2 flex flex-col gap-1 w-full text-body-sm font-medium hide-scrollbar">
            
            <div class="px-3 py-2 text-[10px] text-on-surface-variant/70 font-extrabold uppercase tracking-widest mt-2 mb-1">Menu Utama</div>
            
            <a class="flex items-center w-full h-[40px] px-3 rounded-lg transition-all duration-200 group {{ request()->routeIs('dashboard') ? 'bg-on-surface text-on-primary font-bold shadow-sm' : 'hover:bg-surface-container text-on-surface-variant hover:text-on-surface' }}" href="{{ route('dashboard') }}">
                <span class="material-symbols-outlined mr-3 text-[20px] transition-transform group-hover:scale-110" style="{{ request()->routeIs('dashboard') ? "font-variation-settings: 'FILL' 1;" : '' }}">dashboard</span>
                <span>Dashboard</span>
            </a>
            
            <div class="px-3 py-2 text-[10px] text-on-surface-variant/70 font-extrabold uppercase tracking-widest mt-4 mb-1">Master Data</div>
            
            <a class="flex items-center w-full h-[40px] px-3 rounded-lg transition-all duration-200 group {{ request()->routeIs('pegawai.*') ? 'bg-on-surface text-on-primary font-bold shadow-sm' : 'hover:bg-surface-container text-on-surface-variant hover:text-on-surface' }}" href="{{ route('pegawai.index') }}">
                <span class="material-symbols-outlined mr-3 text-[20px] transition-transform group-hover:scale-110" style="{{ request()->routeIs('pegawai.*') ? "font-variation-settings: 'FILL' 1;" : '' }}">groups</span>
                <span>Data Pegawai</span>
            </a>

            <div class="px-3 py-2 text-[10px] text-on-surface-variant/70 font-extrabold uppercase tracking-widest mt-4 mb-1">Penugasan</div>

            <a class="flex items-center w-full h-[40px] px-3 rounded-lg transition-all duration-200 group {{ request()->routeIs('pengawasan.*') && !request()->routeIs('laporan.*') && !request()->routeIs('rekap.*') ? 'bg-on-surface text-on-primary font-bold shadow-sm' : 'hover:bg-surface-container text-on-surface-variant hover:text-on-surface' }}" href="{{ route('pengawasan.index') }}">
                <span class="material-symbols-outlined mr-3 text-[20px] transition-transform group-hover:scale-110" style="{{ request()->routeIs('pengawasan.*') && !request()->routeIs('laporan.*') && !request()->routeIs('rekap.*') ? "font-variation-settings: 'FILL' 1;" : '' }}">assignment</span>
                <span>Pengawasan</span>
            </a>
            
            <a class="flex items-center w-full h-[40px] px-3 rounded-lg transition-all duration-200 group {{ request()->routeIs('laporan.*') ? 'bg-on-surface text-on-primary font-bold shadow-sm' : 'hover:bg-surface-container text-on-surface-variant hover:text-on-surface' }}" href="{{ route('laporan.index') }}">
                <span class="material-symbols-outlined mr-3 text-[20px] transition-transform group-hover:scale-110" style="{{ request()->routeIs('laporan.*') ? "font-variation-settings: 'FILL' 1;" : '' }}">analytics</span>
                <span>Laporan</span>
            </a>
            
            <a class="flex items-center w-full h-[40px] px-3 rounded-lg transition-all duration-200 group {{ request()->routeIs('rekap.*') ? 'bg-on-surface text-on-primary font-bold shadow-sm' : 'hover:bg-surface-container text-on-surface-variant hover:text-on-surface' }}" href="{{ route('rekap.index') }}">
                <span class="material-symbols-outlined mr-3 text-[20px] transition-transform group-hover:scale-110" style="{{ request()->routeIs('rekap.*') ? "font-variation-settings: 'FILL' 1;" : '' }}">table_chart</span>
                <span>Rekap Data</span>
            </a>

            <div class="px-3 py-2 text-[10px] text-on-surface-variant/70 font-extrabold uppercase tracking-widest mt-4 mb-1">Pengaturan</div>

            <a class="flex items-center w-full h-[40px] px-3 rounded-lg transition-all duration-200 group {{ request()->routeIs('setting.*') ? 'bg-on-surface text-on-primary font-bold shadow-sm' : 'hover:bg-surface-container text-on-surface-variant hover:text-on-surface' }}" href="{{ route('setting.index') }}">
                <span class="material-symbols-outlined mr-3 text-[20px] transition-transform group-hover:scale-110" style="{{ request()->routeIs('setting.*') ? "font-variation-settings: 'FILL' 1;" : '' }}">settings</span>
                <span>Setting Akun</span>
            </a>

        </div>
        
        <!-- Footer / Logout -->
        <div class="p-4 border-t border-border-subtle/50 bg-surface/50">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center w-full h-[40px] text-error hover:bg-error/10 rounded-lg px-3 transition-all duration-200 font-bold group">
                    <span class="material-symbols-outlined mr-3 text-[20px] transition-transform group-hover:-translate-x-1">logout</span>
                    <span>Keluar Sistem</span>
                </button>
            </form>
        </div>
    </div>
</nav>
