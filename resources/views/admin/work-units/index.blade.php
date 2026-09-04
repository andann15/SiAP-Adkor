<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Kelola Unit Kerja" description="Atur daftar Unit Kerja berdasarkan hierarki Kompartemen dan Departemen di PKT.">
            <x-slot name="action">
                <x-ui.button href="{{ route('admin.work-units.create') }}">+ Tambah Unit Kerja</x-ui.button>
            </x-slot>
        </x-ui.page-header>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('success'))
                <div class="p-3 bg-green-100 text-green-800 text-sm rounded">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="p-3 bg-red-100 text-red-800 text-sm rounded">{{ session('error') }}</div>
            @endif

            <x-ui.card :padded="false">
                <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <form method="GET" action="{{ route('admin.work-units.index') }}" class="flex items-center gap-2">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari kompartemen, departemen, atau unit kerja..." class="pl-10 rounded-lg border-gray-200 text-sm focus:ring-brand focus:border-brand w-80">
                        </div>
                        <button type="submit" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium transition-colors">Cari</button>
                        @if($search ?? false)
                            <a href="{{ route('admin.work-units.index') }}" class="px-3 py-2 text-gray-500 hover:text-gray-700 text-sm">Reset</a>
                        @endif
                    </form>
                    <p class="text-xs text-gray-400">{{ $workUnits->total() }} data ditemukan</p>
                </div>

                <table class="min-w-full divide-y divide-gray-100">
                    <thead>
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500">Kompartemen</th>
                            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500">Departemen</th>
                            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500">Unit Kerja</th>
                            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500">Status</th>
                            <th class="px-5 py-3 text-right text-xs font-medium text-gray-500">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($workUnits as $workUnit)
                            <tr class="hover:bg-gray-50 {{ !$workUnit->is_active ? 'opacity-50' : '' }}">
                                <td class="px-5 py-3 text-sm text-gray-800">{{ $workUnit->department?->compartment?->name ?? '-' }}</td>
                                <td class="px-5 py-3 text-sm text-gray-600">{{ $workUnit->department?->name ?? '-' }}</td>
                                <td class="px-5 py-3 text-sm text-gray-500">{{ $workUnit->name ?? '-' }}</td>
                                <td class="px-5 py-3"><x-ui.badge :active="$workUnit->is_active" /></td>
                                <td class="px-5 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.work-units.edit', $workUnit) }}" class="text-blue-600 hover:underline text-sm font-medium">Edit</a>
                                        <form action="{{ route('admin.work-units.toggle', $workUnit) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-gray-500 hover:text-blue-600 hover:underline text-sm font-medium">
                                                {{ $workUnit->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.work-units.destroy', $workUnit) }}" method="POST" class="inline"
                                              onsubmit="return confirm('Hapus Unit Kerja ini? Tindakan ini tidak bisa dibatalkan.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 hover:underline text-sm font-medium">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-5 py-6 text-center text-sm text-gray-500">
                                @if($search ?? false)
                                    Tidak ada hasil untuk "<strong>{{ $search }}</strong>".
                                @else
                                    Belum ada data unit kerja.
                                @endif
                            </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </x-ui.card>

            <div>{{ $workUnits->links() }}</div>
        </div>
    </div>
</x-app-layout>
