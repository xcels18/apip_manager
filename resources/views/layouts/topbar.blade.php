<header class="bg-surface dark:bg-surface-dim text-primary dark:text-primary-fixed font-body-md docked full-width top-0 sticky border-b border-outline-variant flat no shadows flex justify-between items-center h-16 w-full pl-0 md:pl-sidebar-width pr-4 md:pr-margin-desktop z-40">
    <div class="flex items-center h-full px-4 md:px-margin-desktop w-full justify-between">
        <!-- Left side: Mobile Brand (hidden on desktop) / Search -->
        <div class="flex items-center gap-4 flex-1">
            <div class="md:hidden flex flex-col justify-center text-label-md font-bold text-primary">
                <span>APIP INSPEKTORAT KAB. PUNCAK JAYA</span>
            </div>
            <!-- Search Bar -->
            <div class="hidden md:flex relative max-w-sm w-full">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[20px]">search</span>
                <input class="w-full pl-10 pr-4 py-2 h-10 bg-surface-container-lowest border border-outline-variant rounded text-body-sm font-body-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors" placeholder="Cari data..." type="text"/>
            </div>
        </div>
        <!-- Right side actions -->
        <div class="flex items-center gap-2 md:gap-4 shrink-0">
            <!-- Icon Actions -->
            <div class="flex items-center gap-1">
                <button class="w-10 h-10 rounded-full flex items-center justify-center text-on-surface-variant hover:bg-surface-container-high transition-colors Active: opacity-80">
                    <span class="material-symbols-outlined text-[20px]">notifications</span>
                </button>
            </div>
            <!-- Avatar -->
            <div class="w-8 h-8 rounded-full bg-surface-container-high border border-outline-variant overflow-hidden ml-2 flex items-center justify-center text-on-surface-variant">
                <span class="material-symbols-outlined text-[20px]">person</span>
            </div>
            <div class="text-label-md text-on-surface-variant hidden md:block">
                {{ Auth::user()->name }}
            </div>
        </div>
    </div>
</header>
