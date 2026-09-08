<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Kelola Tiket" description="Kelola tiket kerusakan aset dan laporan kendala.">
            <x-slot name="action">
                <div class="flex items-center gap-2">
                    <a href="{{ route('tickets.export-csv', request()->except('page')) }}" class="group inline-flex items-center gap-2 px-4 py-2 border border-gray-200 text-gray-600 hover:border-brand hover:text-brand transition-colors duration-200 rounded-lg text-sm font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Unduh CSV
                    </a>
                    <a href="{{ route('tickets.export-pdf', request()->except('page')) }}" target="_blank" class="group inline-flex items-center gap-2 px-4 py-2 border border-gray-200 text-gray-600 hover:border-brand hover:text-brand transition-colors duration-200 rounded-lg text-sm font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        Cetak PDF
                    </a>
                    @can('create', App\Models\Ticket::class)
                    <a href="{{ route('tickets.create') }}" class="group inline-flex items-center gap-2 px-4 py-2 bg-brand text-sidebar hover:bg-brand/90 transition-colors duration-200 rounded-lg shadow-sm font-medium text-sm">
                        <svg class="w-4 h-4 group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Buat Tiket
                    </a>
                    @endcan
                </div>
            </x-slot>
        </x-ui.page-header>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-green-200 flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    {{ session('success') }}
                </div>
            @endif

            <x-ui.card :padded="false">
                <div class="p-4 border-b border-gray-100 bg-gray-50/50">
                    <form method="GET" action="{{ route('tickets.index') }}" class="flex flex-wrap items-center gap-2">
                        <input type="hidden" name="tab" value="{{ request('tab', 'all') }}">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari no tiket, pembuat, atau aset..." class="pl-10 rounded-lg border-gray-200 text-sm focus:ring-brand focus:border-brand w-60">
                        </div>
                        <select name="status" class="rounded-lg border-gray-200 text-sm focus:ring-brand focus:border-brand">
                            <option value="">Semua Status</option>
                            <option value="waiting_approval" {{ request('status') == 'waiting_approval' ? 'selected' : '' }}>Menunggu Persetujuan</option>
                            <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>Ditugaskan</option>
                            <option value="checking" {{ request('status') == 'checking' ? 'selected' : '' }}>Sedang Diperiksa</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                            <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Ditutup</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                        <select name="priority" class="rounded-lg border-gray-200 text-sm focus:ring-brand focus:border-brand">
                            <option value="">Semua Prioritas</option>
                            @foreach($priorities as $p)
                                <option value="{{ $p->id }}" {{ request('priority') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="px-4 py-2 bg-brand text-sidebar hover:bg-brand/90 rounded-lg text-sm font-medium transition-colors">Filter</button>
                        @if(request()->anyFilled(['search','status','priority']))
                            <a href="{{ route('tickets.index', ['tab' => request('tab', 'all')]) }}" class="px-3 py-2 text-gray-500 hover:text-red-600 text-sm border border-gray-200 rounded-lg">✕ Reset</a>
                        @endif
                    </form>
                    <p class="text-xs text-gray-400 mt-2">{{ $tickets->total() }} tiket ditemukan</p>
                </div>

                <div class="border-b border-gray-100 bg-white flex overflow-x-auto">
                    @php
                        $tabs = [
                            'all' => 'SEMUA',
                            'waiting' => 'MENUNGGU PERSETUJUAN',
                            'in_progress' => 'SEDANG DIKERJAKAN',
                            'completed' => 'SELESAI',
                            'cancelled' => 'DIBATALKAN / DITOLAK',
                        ];
                    @endphp
                    @foreach($tabs as $key => $label)
                        <a href="{{ route('tickets.index', ['tab' => $key, 'search' => request('search')]) }}"
                           class="whitespace-nowrap px-6 py-3.5 font-medium text-sm transition-colors duration-200 border-b-2 {{ $activeTab === $key ? 'border-brand text-brand' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            {{ $label }} ({{ $counts[$key] ?? 0 }})
                        </a>
                    @endforeach
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tiket & Aset</th>
                                <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Pembuat</th>
                                <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Deskripsi</th>
                                <th class="px-5 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Prioritas</th>
                                <th class="px-5 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-5 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse ($tickets as $ticket)
                                <tr class="hover:bg-slate-50 transition-colors duration-200">
                                    <td class="px-5 py-4">
                                        <div class="flex flex-col">
                                            <span class="font-mono text-sm font-semibold text-brand">{{ $ticket->ticket_number ?? '-' }}</span>
                                            <span class="text-sm text-gray-900 mt-1">{{ $ticket->asset?->name ?? '-' }}</span>
                                            <span class="text-xs text-gray-400 mt-0.5">{{ $ticket->created_at->format('d M Y H:i') }}</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex flex-col">
                                            <span class="font-medium text-gray-900">{{ $ticket->creator->name ?? '-' }}</span>
                                            <span class="text-xs text-gray-500">{{ $ticket->creator?->workUnit?->name ?? '-' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 text-sm text-gray-700 max-w-xs">
                                        <div class="line-clamp-2 text-xs leading-relaxed text-gray-600">{{ $ticket->description }}</div>
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        <span class="text-sm font-medium text-gray-700">{{ $ticket->priority?->name ?? '-' }}</span>
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        <x-ticket-status-badge :status="$ticket->status" />
                                    </td>
                                    <td class="px-5 py-4 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <a href="{{ route('tickets.show', $ticket) }}"
                                               class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-md transition-colors">
                                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                Detail
                                            </a>
                                            @can('delete', $ticket)
                                                <form method="POST" action="{{ route('tickets.destroy', $ticket) }}" onsubmit="return confirm('Hapus tiket ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100 rounded-md transition-colors">
                                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                        Hapus
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-16 text-center text-gray-500">
                                        <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                        <p class="text-base font-medium text-gray-900 mb-1">Belum ada tiket</p>
                                        <p class="text-sm text-gray-500 mb-4">Buat tiket kendala pertama Anda.</p>
                                        @can('create', App\Models\Ticket::class)
                                        <a href="{{ route('tickets.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-brand text-sidebar rounded-lg text-sm font-medium hover:bg-brand/90 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                            Buat Sekarang
                                        </a>
                                        @endcan
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($tickets->hasPages())
                    <div class="px-5 py-3 border-t border-gray-100">
                        {{ $tickets->links() }}
                    </div>
                @endif
            </x-ui.card>
        </div>
    </div>
</x-app-layout>