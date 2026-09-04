<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Kelola Status Aset Unit" description="Tambah, ubah, atau hapus pilihan status yang tersedia di form Aset Unit Kerja.">
            <x-slot name="action">
                <a href="{{ route('admin.work-unit-asset-statuses.create') }}" class="group inline-flex items-center gap-2 px-4 py-2 bg-brand text-sidebar hover:bg-brand/90 transition-colors duration-200 rounded-lg shadow-sm font-medium text-sm">
                    <svg class="w-4 h-4 group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Status
                </a>
            </x-slot>
        </x-ui.page-header>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if(session('success'))
                <div class="p-3 bg-green-100 text-green-800 text-sm rounded-lg">{{ session('success') }}</div>
            @endif

            <x-ui.card :padded="false">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Status</th>
                                <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Slug (Kunci)</th>
                                <th class="px-5 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Urutan</th>
                                <th class="px-5 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-5 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse ($statuses as $status)
                                <tr class="hover:bg-slate-50 transition-colors duration-200">
                                    <td class="px-5 py-3 font-semibold text-gray-900 text-sm">{{ $status->name }}</td>
                                    <td class="px-5 py-3 text-xs font-mono text-gray-500">{{ $status->slug }}</td>
                                    <td class="px-5 py-3 text-center text-sm text-gray-600">{{ $status->order }}</td>
                                    <td class="px-5 py-3 text-center">
                                        @if($status->is_active)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">Aktif</span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            {{-- Toggle aktif/nonaktif --}}
                                            <form method="POST" action="{{ route('admin.work-unit-asset-statuses.toggle', $status) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium {{ $status->is_active ? 'text-amber-700 bg-amber-50 hover:bg-amber-100' : 'text-emerald-700 bg-emerald-50 hover:bg-emerald-100' }} rounded-md transition-colors">
                                                    {{ $status->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                                </button>
                                            </form>
                                            {{-- Edit --}}
                                            <a href="{{ route('admin.work-unit-asset-statuses.edit', $status) }}" class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-md transition-colors">
                                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                Edit
                                            </a>
                                            {{-- Hapus --}}
                                            <form method="POST" action="{{ route('admin.work-unit-asset-statuses.destroy', $status) }}" onsubmit="return confirm('Hapus status ini? Aset yang menggunakan status ini akan terpengaruh.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100 rounded-md transition-colors">
                                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-10 text-center text-gray-500">
                                        <p class="font-medium">Belum ada status.</p>
                                        <a href="{{ route('admin.work-unit-asset-statuses.create') }}" class="text-brand hover:underline text-sm mt-1 inline-block">Tambah status pertama</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-ui.card>
        </div>
    </div>
</x-app-layout>
