<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pusat Pemulihan (Recovery Center)') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ activeTab: 'assets' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-green-200">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-[0_8px_30px_rgb(0,0,0,0.04)] sm:rounded-lg">
                <!-- TABS HEADER -->
                <div class="border-b border-gray-200 flex flex-wrap bg-gray-50/50">
                    <button @click="activeTab = 'assets'" 
                            class="px-6 py-4 text-sm font-medium transition-colors duration-200 border-b-2"
                            :class="activeTab === 'assets' ? 'border-brand text-brand bg-white' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-100'">
                        Aset Individu ({{ $individualAssets->count() }})
                    </button>
                    <button @click="activeTab = 'work-unit-assets'" 
                            class="px-6 py-4 text-sm font-medium transition-colors duration-200 border-b-2"
                            :class="activeTab === 'work-unit-assets' ? 'border-brand text-brand bg-white' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-100'">
                        Aset Unit Kerja ({{ $workUnitAssets->count() }})
                    </button>
                    <button @click="activeTab = 'tickets'" 
                            class="px-6 py-4 text-sm font-medium transition-colors duration-200 border-b-2"
                            :class="activeTab === 'tickets' ? 'border-brand text-brand bg-white' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-100'">
                        Tiket ({{ $tickets->count() }})
                    </button>
                </div>

                <!-- TABS CONTENT -->
                <div class="p-6">
                    
                    <!-- TAB: ASET INDIVIDU -->
                    <div x-show="activeTab === 'assets'" style="display: none;">
                        <h3 class="text-lg font-medium mb-4 text-gray-800">Aset Individu yang Dihapus</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 border border-gray-100 rounded-lg">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Kode</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Nama</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Kategori</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Dihapus Pada</th>
                                        <th class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    @forelse ($individualAssets as $asset)
                                        <tr class="hover:bg-slate-50">
                                            <td class="px-4 py-3 font-mono text-sm">{{ $asset->code }}</td>
                                            <td class="px-4 py-3 font-medium text-gray-900">{{ $asset->name }}</td>
                                            <td class="px-4 py-3 text-sm">{{ $asset->category->name ?? '-' }}</td>
                                            <td class="px-4 py-3 text-sm">{{ $asset->deleted_at->format('d M Y H:i') }}</td>
                                            <td class="px-4 py-3 text-right">
                                                <div class="flex justify-end gap-3">
                                                    <form action="{{ route('admin.assets.restore', $asset->id) }}" method="POST" onsubmit="return confirm('Kembalikan aset ini?');">
                                                        @csrf
                                                        <button type="submit" class="text-blue-600 hover:text-blue-800 text-sm font-medium hover:underline">Restore</button>
                                                    </form>
                                                    <form action="{{ route('admin.assets.force-delete', $asset->id) }}" method="POST" onsubmit="return confirm('HAPUS PERMANEN aset ini? Tindakan ini tidak dapat dibatalkan!');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium hover:underline">Hapus Permanen</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                                <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                Tidak ada data aset individu di tempat sampah.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- TAB: ASET UNIT KERJA -->
                    <div x-show="activeTab === 'work-unit-assets'" style="display: none;">
                        <h3 class="text-lg font-medium mb-4 text-gray-800">Aset Unit Kerja yang Dihapus</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 border border-gray-100 rounded-lg">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Kode</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Nama</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Unit Kerja</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Dihapus Pada</th>
                                        <th class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    @forelse ($workUnitAssets as $asset)
                                        <tr class="hover:bg-slate-50">
                                            <td class="px-4 py-3 font-mono text-sm">{{ $asset->code }}</td>
                                            <td class="px-4 py-3 font-medium text-gray-900">{{ $asset->name }}</td>
                                            <td class="px-4 py-3 text-sm">
                                                @php
                                                    $parts = array_filter([
                                                        $asset->workUnit?->department?->compartment?->name,
                                                        $asset->workUnit?->department?->name,
                                                        $asset->workUnit?->name,
                                                    ]);
                                                @endphp
                                                {{ $asset->workUnit ? implode(' › ', $parts) : '-' }}
                                            </td>
                                            <td class="px-4 py-3 text-sm">{{ $asset->deleted_at->format('d M Y H:i') }}</td>
                                            <td class="px-4 py-3 text-right">
                                                <div class="flex justify-end gap-3">
                                                    <form action="{{ route('admin.work-unit-assets.restore', $asset->id) }}" method="POST" onsubmit="return confirm('Kembalikan aset ini?');">
                                                        @csrf
                                                        <button type="submit" class="text-blue-600 hover:text-blue-800 text-sm font-medium hover:underline">Restore</button>
                                                    </form>
                                                    <form action="{{ route('admin.work-unit-assets.force-delete', $asset->id) }}" method="POST" onsubmit="return confirm('HAPUS PERMANEN aset ini? Tindakan ini tidak dapat dibatalkan!');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium hover:underline">Hapus Permanen</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                                <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                Tidak ada data aset unit kerja di tempat sampah.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- TAB: TIKET -->
                    <div x-show="activeTab === 'tickets'" style="display: none;">
                        <h3 class="text-lg font-medium mb-4 text-gray-800">Tiket yang Dihapus</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 border border-gray-100 rounded-lg">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">No. Tiket</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Aset</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Pembuat</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Dihapus Pada</th>
                                        <th class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    @forelse ($tickets as $ticket)
                                        <tr class="hover:bg-slate-50">
                                            <td class="px-4 py-3 font-mono text-sm font-semibold">{{ $ticket->ticket_number }}</td>
                                            <td class="px-4 py-3 text-sm">{{ $ticket->asset->name ?? '-' }}</td>
                                            <td class="px-4 py-3 text-sm">{{ $ticket->creator->name ?? '-' }}</td>
                                            <td class="px-4 py-3 text-sm">{{ $ticket->deleted_at->format('d M Y H:i') }}</td>
                                            <td class="px-4 py-3 text-right">
                                                <div class="flex justify-end gap-3">
                                                    <form action="{{ route('tickets.restore', $ticket->id) }}" method="POST" onsubmit="return confirm('Kembalikan tiket ini?');">
                                                        @csrf
                                                        <button type="submit" class="text-blue-600 hover:text-blue-800 text-sm font-medium hover:underline">Restore</button>
                                                    </form>
                                                    <form action="{{ route('tickets.force-delete', $ticket->id) }}" method="POST" onsubmit="return confirm('HAPUS PERMANEN tiket ini? Tindakan ini tidak dapat dibatalkan!');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium hover:underline">Hapus Permanen</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                                <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                Tidak ada tiket di tempat sampah.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
