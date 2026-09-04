<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Manajemen Pengguna" description="Kelola hak akses dan data pengguna sistem.">
            <x-slot name="action">
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.users.export-csv') }}" class="group inline-flex items-center gap-2 px-4 py-2 border border-gray-200 text-gray-600 hover:border-brand hover:text-brand transition-colors duration-200 rounded-lg text-sm font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Unduh CSV
                    </a>
                    <a href="{{ route('admin.users.export-pdf') }}" class="group inline-flex items-center gap-2 px-4 py-2 border border-gray-200 text-gray-600 hover:border-brand hover:text-brand transition-colors duration-200 rounded-lg text-sm font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Unduh PDF
                    </a>
                    <a href="{{ route('admin.users.create') }}" class="group inline-flex items-center gap-2 px-4 py-2 bg-brand text-sidebar hover:bg-brand/90 transition-colors duration-200 rounded-lg shadow-sm font-medium text-sm">
                        <svg class="w-4 h-4 group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Pengguna
                    </a>
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
                <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <form method="GET" action="{{ route('admin.users.index') }}" class="flex items-center gap-2">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari NIK/NPK, nama, atau unit kerja..." class="pl-10 rounded-lg border-gray-200 text-sm focus:ring-brand focus:border-brand w-72">
                        </div>
                        <button type="submit" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium transition-colors">Cari</button>
                        @if(request('search'))
                            <a href="{{ route('admin.users.index') }}" class="px-3 py-2 text-gray-500 hover:text-gray-700 text-sm">Reset</a>
                        @endif
                    </form>
                    <p class="text-xs text-gray-400">{{ $users->total() }} pengguna ditemukan</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Pengguna</th>
                                <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Kontak</th>
                                <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Unit Kerja</th>
                                <th class="px-5 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Role</th>
                                <th class="px-5 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse ($users as $user)
                                <tr class="hover:bg-slate-50 transition-colors duration-200">
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            @php
                                                $initials = collect(explode(' ', $user->name))
                                                    ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
                                                    ->take(2)
                                                    ->implode('');
                                            @endphp
                                            <div class="w-10 h-10 rounded-full bg-brand/10 text-brand flex items-center justify-center text-sm font-bold flex-shrink-0">
                                                {{ $initials }}
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="font-bold text-slate-900">{{ $user->name }}</span>
                                                <span class="text-xs text-gray-500 font-mono mt-0.5">NIK / NPK: {{ $user->nik ?? '-' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-medium text-gray-800">{{ $user->email }}</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 text-sm text-gray-700">
                                        {{ $user->workUnit->name ?? '-' }}
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->hasRole('admin') ? 'bg-red-100 text-red-800' : ($user->hasRole('operator') ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                            {{ strtoupper($user->roles->first()?->name ?? 'USER') }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <a href="{{ route('admin.users.edit', $user) }}"
                                               class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-md transition-colors">
                                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                Edit
                                            </a>
                                            @if (auth()->id() !== $user->id)
                                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Hapus pengguna ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100 rounded-md transition-colors">
                                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                        Hapus
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-16 text-center text-gray-500">
                                        <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                        <p class="text-base font-medium text-gray-900 mb-1">Belum ada pengguna</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($users->hasPages())
                    <div class="px-5 py-3 border-t border-gray-100">
                        {{ $users->links() }}
                    </div>
                @endif
            </x-ui.card>
        </div>
    </div>
</x-app-layout>
