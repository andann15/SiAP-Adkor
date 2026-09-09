<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Kelola Aset Individu" description="Pantau dan kelola aset individu yang tidak terikat pada unit kerja.">
            <x-slot name="action">
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.assets.export-csv', request()->all()) }}" class="group inline-flex items-center gap-2 px-4 py-2 border border-gray-200 text-gray-600 hover:border-brand hover:text-brand transition-colors duration-200 rounded-lg text-sm font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Unduh CSV
                    </a>
                    <a href="{{ route('admin.assets.export-pdf', request()->all()) }}" target="_blank" class="group inline-flex items-center gap-2 px-4 py-2 border border-gray-200 text-gray-600 hover:border-brand hover:text-brand transition-colors duration-200 rounded-lg text-sm font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        Cetak PDF
                    </a>
                    @can('assets.create')
                    <a href="{{ route('admin.assets.create') }}" class="group inline-flex items-center gap-2 px-4 py-2 bg-brand text-sidebar hover:bg-brand/90 transition-colors duration-200 rounded-lg shadow-sm font-medium text-sm">
                        <svg class="w-4 h-4 group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Aset
                    </a>
                    @endcan
                </div>
            </x-slot>
        </x-ui.page-header>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if(session('success'))
                <div class="p-3 bg-green-100 text-green-800 text-sm rounded-lg">{{ session('success') }}</div>
            @endif

            <x-ui.card :padded="false">
                <div class="p-4 border-b border-gray-100 bg-gray-50/50">
                    <form method="GET" action="{{ route('admin.assets.index') }}" class="flex flex-wrap items-center gap-2">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, model, pengguna..." class="pl-10 rounded-lg border-gray-200 text-sm focus:ring-brand focus:border-brand w-64">
                        </div>
                        <select name="category" class="rounded-lg border-gray-200 text-sm focus:ring-brand focus:border-brand">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        <select name="location" class="rounded-lg border-gray-200 text-sm focus:ring-brand focus:border-brand">
                            <option value="">Semua Lokasi</option>
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}" {{ request('location') == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                            @endforeach
                        </select>
                        <select name="status" class="rounded-lg border-gray-200 text-sm focus:ring-brand focus:border-brand">
                            <option value="">Semua Status</option>
                            @foreach($statuses as $s)
                                <option value="{{ $s->slug }}" {{ request('status') == $s->slug ? 'selected' : '' }}>{{ $s->name }}</option>
                            @endforeach
                        </select>
                        <select name="user_id" class="rounded-lg border-gray-200 text-sm focus:ring-brand focus:border-brand">
                            <option value="">Semua Pengguna</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="px-4 py-2 bg-brand text-sidebar hover:bg-brand/90 rounded-lg text-sm font-medium transition-colors">Filter</button>
                        @if(request()->anyFilled(['search','category','location','status','user_id']))
                            <a href="{{ route('admin.assets.index') }}" class="px-3 py-2 text-gray-500 hover:text-red-600 text-sm border border-gray-200 rounded-lg">✕ Reset</a>
                        @endif
                    </form>
                    <p class="text-xs text-gray-400 mt-2">{{ $assets->total() }} aset ditemukan</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Aset</th>
                                <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Kategori & Merek</th>
                                <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Model & Pengguna</th>
                                <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Lokasi</th>
                                <th class="px-5 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-5 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse ($assets as $asset)
                                <tr class="hover:bg-slate-50 transition-colors duration-200">
                                    <td class="px-5 py-4">
                                        <div class="flex flex-col">
                                            <span class="font-bold text-slate-900">{{ $asset->name }}</span>
                                            <span class="text-xs text-gray-500 font-mono mt-0.5">{{ $asset->code }}</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-medium text-gray-800">{{ $asset->category->name }}</span>
                                            <span class="text-xs text-gray-500 mt-0.5">{{ $asset->brand->name ?? '-' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-medium text-gray-800">{{ $asset->model ?? '-' }}</span>
                                            <span class="text-xs text-gray-500 mt-0.5">{{ $asset->currentUser?->name ?? '-' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 text-sm text-gray-700">
                                        {{ $asset->location->name ?? '-' }}
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        @php
                                            $s = $statuses[$asset->status] ?? null;
                                            $statusName = $s ? $s->name : $asset->status;
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ $statusName }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            @can('assets.edit')
                                                {{-- Edit --}}
                                                <a href="{{ route('admin.assets.edit', $asset) }}"
                                                   class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-md transition-colors">
                                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                    Edit
                                                </a>
                                                @role('admin')
                                                    {{-- Hapus --}}
                                                    <form method="POST" action="{{ route('admin.assets.destroy', $asset) }}" onsubmit="return confirm('Hapus aset ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100 rounded-md transition-colors">
                                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                            Hapus
                                                        </button>
                                                    </form>
                                                @endrole
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-16 text-center text-gray-500">
                                        <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                        <p class="text-base font-medium text-gray-900 mb-1">Belum ada aset</p>
                                        <p class="text-sm text-gray-500 mb-4">Tambahkan aset pertama Anda.</p>
                                        @can('assets.create')
                                        <a href="{{ route('admin.assets.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-brand text-sidebar rounded-lg text-sm font-medium hover:bg-brand/90 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                            Tambah Sekarang
                                        </a>
                                        @endcan
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($assets->hasPages())
                    <div class="px-5 py-3 border-t border-gray-100">
                        {{ $assets->links() }}
                    </div>
                @endif
            </x-ui.card>
        </div>
    </div>
</x-app-layout>