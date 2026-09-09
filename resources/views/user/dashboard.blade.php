<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Dashboard User" description="Pantau tiket Anda dan kelola aset yang Anda gunakan." />
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Welcome Card -->
            <x-ui.card>
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Selamat datang, {{ auth()->user()->name }}</h2>
                        <p class="text-sm text-gray-600 mt-1">Gunakan dashboard ini untuk melaporkan kerusakan aset atau mengelola aset yang Anda pakai.</p>
                    </div>
                    <div class="flex gap-2">
                        <x-ui.button href="{{ route('tickets.create') }}">Lapor Kerusakan Baru</x-ui.button>
                    </div>
                </div>
            </x-ui.card>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Aset Saya Section (Takes up 2 columns on large screens) -->
                <div class="lg:col-span-2 space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-900">Aset Saya</h3>
                        
                        <!-- Modal for claiming asset -->
                        <div x-data="{ open: false }">
                            <button @click="open = true" class="inline-flex items-center px-3 py-1.5 bg-sidebar text-white rounded-md text-sm font-medium hover:bg-orange-500 transition-colors">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                Pilih Aset Baru
                            </button>
                            
                            <!-- Modal -->
                            <div x-show="open" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                    <div x-show="open" @click="open = false" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                    <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                        <form method="POST" action="{{ route('my-assets.claim') }}">
                                            @csrf
                                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Pilih Aset untuk Digunakan</h3>
                                                <div class="mt-2">
                                                    <p class="text-sm text-gray-500 mb-4">Pilih aset yang saat ini belum digunakan oleh siapa pun untuk ditambahkan ke daftar Aset Anda.</p>
                                                    <select name="asset_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-brand focus:ring-brand">
                                                        <option value="">-- Pilih Aset --</option>
                                                        @foreach($availableAssets as $asset)
                                                            <option value="{{ $asset->id }}">{{ $asset->code }} - {{ $asset->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-sidebar text-base font-medium text-white hover:bg-orange-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 sm:ml-3 sm:w-auto sm:text-sm">
                                                    Simpan Aset
                                                </button>
                                                <button type="button" @click="open = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sidebar sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                                    Batal
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @forelse ($myAssets as $asset)
                            <div class="bg-white border rounded-xl p-5 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-md transition-shadow">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h4 class="text-lg font-bold text-gray-900">{{ $asset->name }}</h4>
                                        <p class="text-sm text-gray-500">{{ $asset->code }}</p>
                                    </div>
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-50 text-blue-600 border border-blue-100">
                                        {{ $asset->category->name ?? '-' }}
                                    </span>
                                </div>
                                
                                <div class="space-y-1 text-sm text-gray-600 mb-5">
                                    <div class="flex justify-between">
                                        <span class="text-gray-400">Merek:</span>
                                        <span class="font-medium">{{ $asset->brand->name ?? '-' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-400">Model:</span>
                                        <span class="font-medium">{{ $asset->model ?? '-' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-400">Lokasi:</span>
                                        <span class="font-medium">{{ $asset->location->name ?? '-' }}</span>
                                    </div>
                                </div>
                                
                                <div class="pt-3 border-t border-gray-100">
                                    <a href="{{ route('tickets.create', ['asset_id' => $asset->id]) }}" class="inline-flex items-center justify-center w-full px-4 py-2 bg-sidebar text-white hover:bg-orange-500 transition-colors duration-200 border border-transparent rounded-lg font-medium text-sm">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                        Lapor Kerusakan
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full bg-white border rounded-xl py-10 text-center text-gray-500 shadow-[0_8px_30px_rgb(0,0,0,0.04)]">
                                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                <p>Anda belum mengklaim aset apa pun.</p>
                                <p class="text-sm mt-1">Gunakan tombol "Pilih Aset Baru" di atas untuk menambahkan aset.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Riwayat Tiket Section (Takes up 1 column) -->
                <div class="space-y-4">
                    <h3 class="text-lg font-bold text-gray-900">Riwayat Tiket Terakhir</h3>
                    
                    <div class="bg-white rounded-xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden">
                        @forelse($tickets as $ticket)
                            <a href="{{ route('tickets.show', $ticket) }}" class="block border-b last:border-0 hover:bg-gray-50 p-4 transition-colors">
                                <div class="flex justify-between items-start mb-2">
                                    <span class="font-bold text-sm text-gray-900">{{ $ticket->ticket_number }}</span>
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full 
                                        {{ $ticket->status === 'completed' || $ticket->status === 'closed' ? 'bg-green-100 text-green-800' : 
                                           ($ticket->status === 'rejected' || $ticket->status === 'cancelled' ? 'bg-red-100 text-red-800' : 
                                           'bg-blue-100 text-blue-800') }}">
                                        {{ \App\Http\Controllers\UserDashboardController::STATUS_LABELS[$ticket->status] ?? $ticket->status }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 truncate">{{ $ticket->asset->name ?? 'Aset tidak ditemukan' }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ $ticket->created_at->diffForHumans() }}</p>
                            </a>
                        @empty
                            <div class="p-6 text-center text-gray-500">
                                <p>Belum ada riwayat tiket.</p>
                            </div>
                        @endforelse
                        
                        @if($tickets->count() > 0)
                            <div class="p-3 bg-gray-50 text-center border-t">
                                <a href="{{ route('tickets.index') }}" class="text-sm font-medium text-brand hover:text-orange-500">Lihat Semua Tiket &rarr;</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>