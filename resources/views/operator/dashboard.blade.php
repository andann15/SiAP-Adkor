<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-base text-gray-800 leading-tight">Dashboard Operator</h2>
            <p class="text-xs text-gray-500 mt-0.5">Tiket yang ditugaskan kepada Anda beserta status SLA.</p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-5">

            {{-- Sambutan --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-[0_4px_20px_rgb(0,0,0,0.04)] px-5 py-4">
                <p class="text-sm text-gray-600">
                    Selamat datang, <span class="font-semibold text-gray-800">{{ auth()->user()->name }}</span>.
                    Berikut adalah ringkasan tugas perbaikan aset yang sedang Anda tangani.
                </p>
            </div>

            {{-- Kartu Statistik --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-xl border-l-4 border-blue-500 shadow-[0_4px_20px_rgb(0,0,0,0.04)] p-4 hover:-translate-y-1 hover:shadow-md transition-all duration-300">
                    <p class="text-xs text-gray-500 mb-1">Sedang Aktif</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $totalAssigned }}</p>
                    <p class="text-xs text-blue-500 mt-1 font-medium">Tiket perlu ditangani</p>
                </div>
                <div class="bg-white rounded-xl border-l-4 border-red-500 shadow-[0_4px_20px_rgb(0,0,0,0.04)] p-4 hover:-translate-y-1 hover:shadow-md transition-all duration-300">
                    <p class="text-xs text-gray-500 mb-1">SLA Terlambat</p>
                    <p class="text-3xl font-bold {{ $totalSlaBreached > 0 ? 'text-red-600' : 'text-gray-800' }}">{{ $totalSlaBreached }}</p>
                    <p class="text-xs text-red-500 mt-1 font-medium">Melebihi batas waktu</p>
                </div>
                <div class="bg-white rounded-xl border-l-4 border-green-500 shadow-[0_4px_20px_rgb(0,0,0,0.04)] p-4 hover:-translate-y-1 hover:shadow-md transition-all duration-300">
                    <p class="text-xs text-gray-500 mb-1">Selesai Dikerjakan</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $totalCompleted }}</p>
                    <p class="text-xs text-green-600 mt-1 font-medium">Berhasil diselesaikan</p>
                </div>
                <div class="bg-white rounded-xl border-l-4 border-gray-400 shadow-[0_4px_20px_rgb(0,0,0,0.04)] p-4 hover:-translate-y-1 hover:shadow-md transition-all duration-300">
                    <p class="text-xs text-gray-500 mb-1">Total Pernah Dikerjakan</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $totalAllTime }}</p>
                    <p class="text-xs text-gray-500 mt-1 font-medium">Semua tiket</p>
                </div>
            </div>

            {{-- Daftar Tiket Aktif --}}
            <div class="bg-white rounded-xl shadow-[0_4px_20px_rgb(0,0,0,0.04)] overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-700">Tiket Aktif Saya</h3>
                    <a href="{{ route('tickets.index') }}" class="text-xs text-blue-600 hover:underline font-medium">
                        Lihat Semua Tiket &rarr;
                    </a>
                </div>

                {{-- Desktop: Table --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">No. Tiket</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Aset</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Pelapor</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Prioritas</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Deadline SLA</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($tickets as $ticket)
                                <tr class="{{ $ticket->sla_breached ? 'bg-red-50' : 'hover:bg-gray-50' }} transition-colors">
                                    <td class="px-4 py-3 text-sm font-mono font-medium text-gray-700">
                                        TKT-{{ strtoupper(substr($ticket->id, 0, 8)) }}
                                        @if($ticket->sla_breached)
                                            <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-red-100 text-red-700">SLA!</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $ticket->asset->name ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $ticket->creator->name ?? '-' }}</td>
                                    <td class="px-4 py-3">
                                        <x-ticket-priority-dot :priority="$ticket->priority->name ?? null" />
                                    </td>
                                    <td class="px-4 py-3">
                                        <x-ticket-status-badge :status="$ticket->status" />
                                    </td>
                                    <td class="px-4 py-3 text-sm {{ $ticket->sla_breached ? 'text-red-600 font-semibold' : 'text-gray-500' }}">
                                        {{ $ticket->sla_deadline ? $ticket->sla_deadline->format('d M Y') : '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('tickets.show', $ticket) }}" class="inline-flex items-center px-3 py-1.5 bg-gray-800 text-white text-xs font-medium rounded-lg hover:bg-gray-700 transition-colors">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-10 text-center text-sm text-gray-400">
                                        <div class="flex flex-col items-center gap-2">
                                            <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                            <span>Tidak ada tiket aktif yang ditugaskan kepada Anda saat ini.</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Mobile: Card List --}}
                <div class="md:hidden divide-y divide-gray-100">
                    @forelse ($tickets as $ticket)
                        <div class="px-4 py-4 {{ $ticket->sla_breached ? 'bg-red-50' : '' }}">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-mono text-xs font-semibold text-gray-600">TKT-{{ strtoupper(substr($ticket->id, 0, 8)) }}</span>
                                        @if($ticket->sla_breached)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-red-100 text-red-700">SLA!</span>
                                        @endif
                                        <x-ticket-status-badge :status="$ticket->status" />
                                    </div>
                                    <p class="mt-1 text-sm font-semibold text-gray-800 truncate">{{ $ticket->asset->name ?? '-' }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">Pelapor: {{ $ticket->creator->name ?? '-' }}</p>
                                    <div class="flex items-center gap-3 mt-1.5">
                                        <x-ticket-priority-dot :priority="$ticket->priority->name ?? null" />
                                        <span class="text-xs {{ $ticket->sla_breached ? 'text-red-600 font-semibold' : 'text-gray-500' }}">
                                            Deadline: {{ $ticket->sla_deadline ? $ticket->sla_deadline->format('d M Y') : '-' }}
                                        </span>
                                    </div>
                                </div>
                                <a href="{{ route('tickets.show', $ticket) }}" class="flex-shrink-0 inline-flex items-center px-3 py-1.5 bg-gray-800 text-white text-xs font-medium rounded-lg hover:bg-gray-700 transition-colors">
                                    Detail
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="px-4 py-10 text-center text-sm text-gray-400">
                            Tidak ada tiket aktif yang ditugaskan kepada Anda saat ini.
                        </div>
                    @endforelse
                </div>

                @if($tickets->hasPages())
                    <div class="px-5 py-3 border-t border-gray-100">
                        {{ $tickets->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>