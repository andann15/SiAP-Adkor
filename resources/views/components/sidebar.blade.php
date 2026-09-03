<aside class="fixed inset-y-0 left-0 z-40 bg-sidebar text-gray-300 border-none transition-all duration-300 shadow-[0_8px_30px_rgb(0,0,0,0.04)] flex flex-col" 
       :class="sidebarOpen ? 'translate-x-0 w-64' : '-translate-x-full lg:translate-x-0', sidebarCollapsed ? 'lg:w-20' : 'lg:w-64'">
    
    <!-- Bagian Logo & Tombol Hide -->
    <div class="flex items-center justify-between px-4 py-5 border-b border-sidebar/50 shadow-sm min-h-[5rem]">
        <!-- Logo versi Besar (Tampil saat sidebar terbuka) -->
        <div class="flex items-center gap-3 overflow-hidden" x-show="!sidebarCollapsed">
            <x-application-logo class="w-10 h-10 flex-shrink-0" /> 
            <div class="flex flex-col justify-center">
                <span class="text-3xl font-extrabold [font-family:'Poppins',sans-serif] whitespace-nowrap leading-none tracking-tight">
                    <span class="text-white">S</span><span class="text-orange-500">i</span><span class="text-white">AP</span>
                </span>
                <span class="text-[9px] uppercase tracking-[0.08em] font-semibold text-gray-400 mt-1 leading-[1.2]">
                    Sistem Informasi<br>Aset & Pelayanan
                </span>
            </div>
        </div>
        
        <!-- Logo versi Kecil (Tampil saat sidebar disembunyikan) -->
        <div class="flex justify-center w-full" x-show="sidebarCollapsed" style="display: none;">
             <x-application-logo class="w-8 h-8" />
        </div>

        <!-- Tombol Hide/Collapse Sidebar (Muncul di layar besar) -->
        <button @click="sidebarCollapsed = !sidebarCollapsed" class="p-1.5 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-600 hidden lg:flex items-center justify-center flex-shrink-0 transition-all" title="Sembunyikan/Tampilkan Menu">
            <svg class="w-5 h-5 transition-transform duration-300" :class="sidebarCollapsed ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </button>
    </div>

    <!-- Role Badge -->
    <div class="px-6 py-4 transition-opacity" x-show="!sidebarCollapsed">
        @role('admin')
        <span class="inline-flex items-center px-3 py-1 rounded-md text-xs font-extrabold bg-brand text-sidebar tracking-wider">
            PORTAL ADMIN
        </span>
        @else
        <span class="inline-flex items-center px-3 py-1 rounded-md text-xs font-extrabold bg-brand text-sidebar tracking-wider">
            PORTAL KARYAWAN
        </span>
        @endrole
    </div>

    <!-- Navigation Links -->
    <nav class="flex-1 px-4 py-2 space-y-6 overflow-y-auto overflow-x-hidden">
        
        <!-- Section: UTAMA -->
        <div>
            <h3 x-show="!sidebarCollapsed" class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Utama</h3>
            <hr x-show="sidebarCollapsed" class="my-2 border-gray-100" style="display: none;">
            <ul class="space-y-1">
                <li>
                    <a href="{{ route('dashboard') }}" class="group flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-300 {{ request()->routeIs('dashboard') ? 'bg-brand text-sidebar shadow-[0_8px_30px_rgb(0,0,0,0.04)] translate-x-1' : 'text-gray-300 hover:bg-sidebar-light hover:text-white rounded-lg' }}" title="Dashboard">
                        <svg class="w-6 h-6 flex-shrink-0 transition-transform duration-300 {{ request()->routeIs('dashboard') ? 'text-sidebar' : 'text-gray-500 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        <span x-show="!sidebarCollapsed" class="font-medium text-sm whitespace-nowrap {{ request()->routeIs('dashboard') ? 'font-semibold' : '' }}">Dashboard</span>
                    </a>
                </li>
                
                @role('admin')
                <li>
                    <a href="{{ route('tickets.index') }}" class="group flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-300 {{ request()->routeIs('tickets.*') ? 'bg-brand text-sidebar shadow-[0_8px_30px_rgb(0,0,0,0.04)] translate-x-1' : 'text-gray-300 hover:bg-sidebar-light hover:text-white rounded-lg' }}" title="Kelola Tiket">
                        <svg class="w-6 h-6 flex-shrink-0 transition-transform duration-300 {{ request()->routeIs('tickets.*') ? 'text-sidebar' : 'text-gray-500 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        <span x-show="!sidebarCollapsed" class="font-medium text-sm whitespace-nowrap {{ request()->routeIs('tickets.*') ? 'font-semibold' : '' }}">Kelola Tiket</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.work-unit-assets.index') }}" class="group flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-300 {{ request()->routeIs('admin.work-unit-assets.*') ? 'bg-brand text-sidebar shadow-[0_8px_30px_rgb(0,0,0,0.04)] translate-x-1' : 'text-gray-300 hover:bg-sidebar-light hover:text-white rounded-lg' }}" title="Aset Unit Kerja">
                        <svg class="w-6 h-6 flex-shrink-0 transition-transform duration-300 {{ request()->routeIs('admin.work-unit-assets.*') ? 'text-sidebar' : 'text-gray-500 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        <span x-show="!sidebarCollapsed" class="font-medium text-sm whitespace-nowrap {{ request()->routeIs('admin.work-unit-assets.*') ? 'font-semibold' : '' }}">Aset Unit Kerja</span>
                    </a>
                </li>
                @endrole

                @role('user')
                <li>
                    <a href="{{ route('tickets.create') }}" class="group flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-300 {{ request()->routeIs('tickets.create') ? 'bg-brand text-sidebar shadow-md rounded-lg font-bold' : 'text-gray-300 hover:bg-sidebar-light hover:text-white rounded-lg' }}" title="Lapor Kerusakan">
                        <svg class="w-6 h-6 flex-shrink-0 transition-transform duration-300 {{ request()->routeIs('tickets.create') ? 'text-sidebar' : 'text-gray-400 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <span x-show="!sidebarCollapsed" class="font-medium text-sm whitespace-nowrap {{ request()->routeIs('tickets.create') ? 'font-semibold' : '' }}">Lapor Kerusakan</span>
                    </a>
                </li>
                @endrole
            </ul>
        </div>

        <!-- Section: MASTER DATA -->
        @role('admin')
        <div>
            <h3 x-show="!sidebarCollapsed" class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 mt-4">Master Data</h3>
            <hr x-show="sidebarCollapsed" class="my-2 border-gray-100" style="display: none;">
            <ul class="space-y-1">
                <li>
                    <a href="{{ route('admin.assets.index') }}" class="group flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-300 {{ request()->routeIs('admin.assets.*') ? 'bg-brand text-sidebar shadow-[0_8px_30px_rgb(0,0,0,0.04)] translate-x-1' : 'text-gray-300 hover:bg-sidebar-light hover:text-white rounded-lg' }}" title="Kelola Aset">
                        <svg class="w-6 h-6 flex-shrink-0 transition-transform duration-300 {{ request()->routeIs('admin.assets.*') ? 'text-sidebar' : 'text-gray-500 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        <span x-show="!sidebarCollapsed" class="font-medium text-sm whitespace-nowrap {{ request()->routeIs('admin.assets.*') ? 'font-semibold' : '' }}">Kelola Aset</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.work-unit-asset-statuses.index') }}" class="group flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-300 {{ request()->routeIs('admin.work-unit-asset-statuses.*') ? 'bg-brand text-sidebar shadow-[0_8px_30px_rgb(0,0,0,0.04)] translate-x-1' : 'text-gray-300 hover:bg-sidebar-light hover:text-white rounded-lg' }}" title="Status Aset Unit">
                        <svg class="w-6 h-6 flex-shrink-0 transition-transform duration-300 {{ request()->routeIs('admin.work-unit-asset-statuses.*') ? 'text-sidebar' : 'text-gray-500 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                        <span x-show="!sidebarCollapsed" class="font-medium text-sm whitespace-nowrap {{ request()->routeIs('admin.work-unit-asset-statuses.*') ? 'font-semibold' : '' }}">Status Aset Unit</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.work-units.index') }}" class="group flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-300 {{ request()->routeIs('admin.work-units.*') ? 'bg-brand text-sidebar shadow-[0_8px_30px_rgb(0,0,0,0.04)] translate-x-1' : 'text-gray-300 hover:bg-sidebar-light hover:text-white rounded-lg' }}" title="Kelola Unit Kerja">
                        <svg class="w-6 h-6 flex-shrink-0 transition-transform duration-300 {{ request()->routeIs('admin.work-units.*') ? 'text-sidebar' : 'text-gray-500 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        <span x-show="!sidebarCollapsed" class="font-medium text-sm whitespace-nowrap {{ request()->routeIs('admin.work-units.*') ? 'font-semibold' : '' }}">Kelola Unit Kerja</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.asset-categories.index') }}" class="group flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-300 {{ request()->routeIs('admin.asset-categories.*') ? 'bg-brand text-sidebar shadow-[0_8px_30px_rgb(0,0,0,0.04)] translate-x-1' : 'text-gray-300 hover:bg-sidebar-light hover:text-white rounded-lg' }}" title="Kelola Kategori Aset">
                        <svg class="w-6 h-6 flex-shrink-0 transition-transform duration-300 {{ request()->routeIs('admin.asset-categories.*') ? 'text-sidebar' : 'text-gray-500 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                        <span x-show="!sidebarCollapsed" class="font-medium text-sm whitespace-nowrap {{ request()->routeIs('admin.asset-categories.*') ? 'font-semibold' : '' }}">Kelola Kategori Aset</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.brands.index') }}" class="group flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-300 {{ request()->routeIs('admin.brands.*') ? 'bg-brand text-sidebar shadow-[0_8px_30px_rgb(0,0,0,0.04)] translate-x-1' : 'text-gray-300 hover:bg-sidebar-light hover:text-white rounded-lg' }}" title="Kelola Merek">
                        <svg class="w-6 h-6 flex-shrink-0 transition-transform duration-300 {{ request()->routeIs('admin.brands.*') ? 'text-sidebar' : 'text-gray-500 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                        <span x-show="!sidebarCollapsed" class="font-medium text-sm whitespace-nowrap {{ request()->routeIs('admin.brands.*') ? 'font-semibold' : '' }}">Kelola Merek</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.locations.index') }}" class="group flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-300 {{ request()->routeIs('admin.locations.*') ? 'bg-brand text-sidebar shadow-[0_8px_30px_rgb(0,0,0,0.04)] translate-x-1' : 'text-gray-300 hover:bg-sidebar-light hover:text-white rounded-lg' }}" title="Kelola Lokasi">
                        <svg class="w-6 h-6 flex-shrink-0 transition-transform duration-300 {{ request()->routeIs('admin.locations.*') ? 'text-sidebar' : 'text-gray-500 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <span x-show="!sidebarCollapsed" class="font-medium text-sm whitespace-nowrap {{ request()->routeIs('admin.locations.*') ? 'font-semibold' : '' }}">Kelola Lokasi</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.ticket-priorities.index') }}" class="group flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-300 {{ request()->routeIs('admin.ticket-priorities.*') ? 'bg-brand text-sidebar shadow-[0_8px_30px_rgb(0,0,0,0.04)] translate-x-1' : 'text-gray-300 hover:bg-sidebar-light hover:text-white rounded-lg' }}" title="Kelola Prioritas Tiket">
                        <svg class="w-6 h-6 flex-shrink-0 transition-transform duration-300 {{ request()->routeIs('admin.ticket-priorities.*') ? 'text-sidebar' : 'text-gray-500 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span x-show="!sidebarCollapsed" class="font-medium text-sm whitespace-nowrap {{ request()->routeIs('admin.ticket-priorities.*') ? 'font-semibold' : '' }}">Kelola Prioritas Tiket</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.rejection-reasons.index') }}" class="group flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-300 {{ request()->routeIs('admin.rejection-reasons.*') ? 'bg-brand text-sidebar shadow-[0_8px_30px_rgb(0,0,0,0.04)] translate-x-1' : 'text-gray-300 hover:bg-sidebar-light hover:text-white rounded-lg' }}" title="Kelola Alasan Penolakan">
                        <svg class="w-6 h-6 flex-shrink-0 transition-transform duration-300 {{ request()->routeIs('admin.rejection-reasons.*') ? 'text-sidebar' : 'text-gray-500 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span x-show="!sidebarCollapsed" class="font-medium text-sm whitespace-nowrap {{ request()->routeIs('admin.rejection-reasons.*') ? 'font-semibold' : '' }}">Kelola Alasan Penolakan</span>
                    </a>
                </li>
            </ul>
        </div>

        <div>
            <h3 x-show="!sidebarCollapsed" class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 mt-4">Pengaturan</h3>
            <hr x-show="sidebarCollapsed" class="my-2 border-gray-100" style="display: none;">
            <ul class="space-y-1">
                <li>
                    <!-- Use an optional chaining or default '#' if route doesn't exist yet so it doesn't break -->
                    <a href="{{ Route::has('admin.users.index') ? route('admin.users.index') : '#' }}" class="group flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-300 {{ request()->routeIs('admin.users.*') ? 'bg-brand text-sidebar shadow-[0_8px_30px_rgb(0,0,0,0.04)] translate-x-1' : 'text-gray-300 hover:bg-sidebar-light hover:text-white rounded-lg' }}" title="Manajemen Pengguna">
                        <svg class="w-6 h-6 flex-shrink-0 transition-transform duration-300 {{ request()->routeIs('admin.users.*') ? 'text-sidebar' : 'text-gray-500 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        <span x-show="!sidebarCollapsed" class="font-medium text-sm whitespace-nowrap {{ request()->routeIs('admin.users.*') ? 'font-semibold' : '' }}">Manajemen Pengguna</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.recovery.index') }}" class="group flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-300 {{ request()->routeIs('admin.recovery.*') ? 'bg-brand text-sidebar shadow-[0_8px_30px_rgb(0,0,0,0.04)] translate-x-1' : 'text-gray-300 hover:bg-sidebar-light hover:text-white rounded-lg' }}" title="Pusat Pemulihan">
                        <svg class="w-6 h-6 flex-shrink-0 transition-transform duration-300 {{ request()->routeIs('admin.recovery.*') ? 'text-sidebar' : 'text-gray-500 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        <span x-show="!sidebarCollapsed" class="font-medium text-sm whitespace-nowrap {{ request()->routeIs('admin.recovery.*') ? 'font-semibold' : '' }}">Pusat Pemulihan</span>
                    </a>
                </li>
            </ul>
        </div>
        @endrole
    </nav>
</aside>