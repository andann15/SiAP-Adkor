<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-base text-gray-800 leading-tight">
                {{ __('Dashboard Pemeliharaan') }}
            </h2>
            <p class="text-xs text-gray-500 mt-0.5">Ringkasan aset dan tiket pemeliharaan.</p>
        </div>
    </x-slot>

    <div class="py-5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            @if (session('success'))
                <div class="p-3 bg-green-100 text-green-800 text-xs rounded-lg flex items-center gap-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    {{ session('success') }}
                </div>
            @endif

            {{-- Stat Cards: 2 kolom di HP, 4 di desktop --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <div class="bg-white rounded-xl shadow-sm border-l-4 border-orange-400 p-4 hover:-translate-y-1 hover:shadow-md transition-all duration-200 cursor-pointer">
                    <p class="text-xs font-medium text-gray-500 mb-1">Total Tiket</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm border-l-4 border-yellow-400 p-4 hover:-translate-y-1 hover:shadow-md transition-all duration-200 cursor-pointer">
                    <p class="text-xs font-medium text-gray-500 mb-1">Menunggu Persetujuan</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['waiting'] }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm border-l-4 border-blue-400 p-4 hover:-translate-y-1 hover:shadow-md transition-all duration-200 cursor-pointer">
                    <p class="text-xs font-medium text-gray-500 mb-1">Sedang Dikerjakan</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['in_progress'] }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm border-l-4 border-red-400 p-4 hover:-translate-y-1 hover:shadow-md transition-all duration-200 cursor-pointer">
                    <p class="text-xs font-medium text-gray-500 mb-1">SLA Terlambat</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['sla_breached'] }}</p>
                </div>
            </div>

            {{-- Tabel Tiket Terbaru --}}
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-700">Tiket Terbaru</h3>
                    <a href="{{ route('tickets.index') }}" class="text-xs text-blue-600 hover:underline font-medium">Lihat Semua &rarr;</a>
                </div>

                {{-- Mobile card view (hidden di lg+) --}}
                <div class="lg:hidden divide-y divide-gray-100">
                    @forelse ($tickets as $ticket)
                        <div class="p-4 space-y-2">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <span class="font-mono text-xs font-semibold text-gray-600">TKT-{{ strtoupper(substr($ticket->id, 0, 8)) }}</span>
                                    <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ $ticket->asset->name }}</p>
                                </div>
                                <x-ticket-status-badge :status="$ticket->status" />
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <x-ticket-priority-dot :priority="$ticket->priority->name ?? null" />
                                    <span class="text-xs text-gray-500">{{ $ticket->creator->name }}</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-xs text-gray-400">{{ $ticket->created_at->format('d M Y') }}</span>
                                    <a href="{{ route('tickets.show', $ticket) }}"
                                       class="inline-flex items-center px-3 py-1 bg-blue-50 text-blue-700 rounded-md text-xs font-semibold hover:bg-blue-100 transition-colors">
                                        Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="px-4 py-8 text-center text-sm text-gray-500">Belum ada tiket.</div>
                    @endforelse
                </div>

                {{-- Desktop table view (hidden di bawah lg) --}}
                <div class="hidden lg:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">No. Tiket</th>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Nama Aset</th>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Pelapor</th>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Prioritas</th>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Dibuat</th>
                                <th class="px-4 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($tickets as $ticket)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-4 py-3 font-mono text-xs font-semibold text-gray-700">TKT-{{ strtoupper(substr($ticket->id, 0, 8)) }}</td>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ $ticket->asset->name }}</td>
                                    <td class="px-4 py-3 text-xs text-gray-600">{{ $ticket->creator->name }}</td>
                                    <td class="px-4 py-3"><x-ticket-priority-dot :priority="$ticket->priority->name ?? null" /></td>
                                    <td class="px-4 py-3"><x-ticket-status-badge :status="$ticket->status" /></td>
                                    <td class="px-4 py-3 text-xs text-gray-500">{{ $ticket->created_at->format('d M Y') }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('tickets.show', $ticket) }}"
                                           class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-700 rounded-md text-xs font-semibold hover:bg-blue-100 transition-colors">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500">Belum ada tiket.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-4 py-3 border-t border-gray-100">
                    {{ $tickets->links() }}
                </div>
            </div>

            {{-- Akses Cepat --}}
            <div class="bg-white rounded-xl shadow-sm p-4">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Akses Cepat</h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:flex lg:flex-wrap gap-2">
                    <a href="{{ route('tickets.index') }}"
                       class="flex items-center justify-center gap-1.5 px-4 py-2 bg-brand text-sidebar rounded-lg text-xs font-semibold hover:opacity-90 transition-all duration-200 shadow-sm">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        Kelola Tiket
                    </a>
                    <a href="{{ route('admin.assets.index') }}"
                       class="flex items-center justify-center gap-1.5 px-4 py-2 border border-gray-200 text-gray-600 rounded-lg text-xs font-semibold hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700 transition-all duration-200">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/></svg>
                        Kelola Aset
                    </a>
                    <a href="{{ route('admin.work-units.index') }}"
                       class="flex items-center justify-center gap-1.5 px-4 py-2 border border-gray-200 text-gray-600 rounded-lg text-xs font-semibold hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700 transition-all duration-200">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        Kelola Unit Kerja
                    </a>
                    <a href="{{ route('admin.asset-categories.index') }}"
                       class="flex items-center justify-center gap-1.5 px-4 py-2 border border-gray-200 text-gray-600 rounded-lg text-xs font-semibold hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700 transition-all duration-200">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                        Kelola Kategori Aset
                    </a>
                    <a href="{{ route('admin.ticket-priorities.index') }}"
                       class="flex items-center justify-center gap-1.5 px-4 py-2 border border-gray-200 text-gray-600 rounded-lg text-xs font-semibold hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700 transition-all duration-200">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        Kelola Prioritas Tiket
                    </a>
                    <a href="{{ route('admin.rejection-reasons.index') }}"
                       class="flex items-center justify-center gap-1.5 px-4 py-2 border border-gray-200 text-gray-600 rounded-lg text-xs font-semibold hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700 transition-all duration-200">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Kelola Alasan Penolakan
                    </a>
                    <a href="{{ route('admin.brands.index') }}"
                       class="flex items-center justify-center gap-1.5 px-4 py-2 border border-gray-200 text-gray-600 rounded-lg text-xs font-semibold hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700 transition-all duration-200">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        Kelola Merek
                    </a>
                    <a href="{{ route('admin.locations.index') }}"
                       class="flex items-center justify-center gap-1.5 px-4 py-2 border border-gray-200 text-gray-600 rounded-lg text-xs font-semibold hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700 transition-all duration-200">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Kelola Lokasi
                    </a>
                </div>
            </div>

        </div>
</x-app-layout>
