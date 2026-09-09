<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('tickets.index') }}" class="text-gray-500 hover:text-gray-700 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Tiket') }}
        </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="p-4 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
            @endif

            <div class="bg-white overflow-hidden shadow-[0_8px_30px_rgb(0,0,0,0.04)] sm:rounded-lg p-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-lg font-medium">{{ $ticket->asset->name ?? 'Aset tidak ditemukan' }} ({{ $ticket->asset->code ?? '-' }})</h3>
                        <p class="text-sm text-gray-500 mt-1">
                            Dibuat pada {{ $ticket->created_at->format('d M Y H:i') }}
                        </p>
                    </div>
                    <x-ticket-status-badge :status="$ticket->status" />
                </div>

                <dl class="grid grid-cols-2 gap-4 text-sm mb-4">
                    <div>
                        <dt class="text-gray-500">Pembuat Tiket</dt>
                        <dd class="font-medium text-gray-900">{{ $ticket->creator->name ?? '-' }}</dd>
                        <dd class="text-xs text-gray-500">{{ $ticket->creator?->division?->name ?? 'Tanpa Divisi' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Prioritas</dt>
                        <dd>{{ $ticket->priority->name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Operator</dt>
                        <dd>{{ $ticket->assignedOperator->name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">SLA Deadline</dt>
                        <dd>{{ $ticket->sla_deadline?->format('d M Y H:i') ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Alasan Penolakan</dt>
                        <dd>{{ $ticket->rejectionReason->label ?? '-' }}</dd>
                    </div>
                </dl>

                <div class="mb-4">
                    <dt class="text-gray-500 text-sm mb-1">Deskripsi</dt>
                    <dd>{{ $ticket->description }}</dd>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Bukti Laporan Awal</p>
                        @php $ext = pathinfo(parse_url($ticket->photo_url, PHP_URL_PATH), PATHINFO_EXTENSION); @endphp
                        @if(strtolower($ext) === 'pdf')
                            <a href="{{ $ticket->photo_url }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-red-50 text-red-700 rounded-lg text-sm font-medium hover:bg-red-100 transition-colors">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M4 18h12V8l-4-4H4v14z" opacity=".3"/><path d="M9 13H7v-1h2v1zm4 0h-2v-1h2v1zm-4-3H7V9h2v1zm4 0h-2V9h2v1zM8 4H4v14h12V8l-4-4zm6 13H6V5h5v3h3v9z"/></svg>
                                Lihat PDF Laporan
                            </a>
                        @else
                            <img src="{{ $ticket->photo_url }}" class="rounded border max-h-48 object-contain" alt="Foto Laporan">
                        @endif
                    </div>
                    @if ($ticket->proof_photo_url)
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Bukti Perbaikan</p>
                            @php $extProof = pathinfo(parse_url($ticket->proof_photo_url, PHP_URL_PATH), PATHINFO_EXTENSION); @endphp
                            @if(strtolower($extProof) === 'pdf')
                                <a href="{{ $ticket->proof_photo_url }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-red-50 text-red-700 rounded-lg text-sm font-medium hover:bg-red-100 transition-colors">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M4 18h12V8l-4-4H4v14z" opacity=".3"/><path d="M9 13H7v-1h2v1zm4 0h-2v-1h2v1zm-4-3H7V9h2v1zm4 0h-2V9h2v1zM8 4H4v14h12V8l-4-4zm6 13H6V5h5v3h3v9z"/></svg>
                                    Lihat PDF Bukti Perbaikan
                                </a>
                            @else
                                <img src="{{ $ticket->proof_photo_url }}" class="rounded border max-h-48 object-contain" alt="Foto Bukti">
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            {{-- Panel admin: approve / reject --}}
            @can('approve', $ticket)
                <div class="bg-white overflow-hidden shadow-[0_8px_30px_rgb(0,0,0,0.04)] sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium mb-4">Setujui &amp; Tugaskan Operator</h3>
                    <form method="POST" action="{{ route('tickets.approve', $ticket) }}" class="mb-6">
                        @csrf
                        @method('PATCH')
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Operator</label>
                            <select name="assigned_operator_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-[0_8px_30px_rgb(0,0,0,0.04)]">
                                <option value="">Pilih operator</option>
                                @foreach ($operators as $operator)
                                    <option value="{{ $operator->id }}">{{ $operator->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Prioritas</label>
                            <select name="ticket_priority_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-[0_8px_30px_rgb(0,0,0,0.04)]">
                                <option value="">Pilih prioritas</option>
                                @foreach ($priorities as $priority)
                                    <option value="{{ $priority->id }}">{{ $priority->name }} ({{ $priority->sla_hours }} jam)</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="px-4 py-2 bg-sidebar text-white hover:bg-orange-500 border border-transparent rounded transition-colors ">Setujui</button>
                    </form>

                    <h3 class="text-lg font-medium mb-4 pt-4 border-t">Tolak Tiket</h3>
                    <form method="POST" action="{{ route('tickets.reject', $ticket) }}">
                        @csrf
                        @method('PATCH')
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Alasan Penolakan</label>
                            <select name="rejection_reason_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-[0_8px_30px_rgb(0,0,0,0.04)]">
                                <option value="">Pilih alasan</option>
                                @foreach ($rejectionReasons as $reason)
                                    <option value="{{ $reason->id }}">{{ $reason->label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Tolak</button>
                    </form>
                </div>
            @endcan

            {{-- Panel operator --}}
            @can('updateStatus', $ticket)
                <div class="bg-white overflow-hidden shadow-[0_8px_30px_rgb(0,0,0,0.04)] sm:rounded-lg p-6">
                    @if ($ticket->status === 'assigned')
                        <h3 class="text-lg font-medium mb-4">Mulai Pengerjaan</h3>
                        <form method="POST" action="{{ route('tickets.start-checking', $ticket) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="px-4 py-2 bg-sidebar text-white hover:bg-orange-500 border border-transparent rounded transition-colors ">Mulai Periksa</button>
                        </form>
                    @elseif ($ticket->status === 'checking')
                        <h3 class="text-lg font-medium mb-4">Tandai Selesai</h3>
                        <form method="POST" action="{{ route('tickets.complete', $ticket) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PATCH')
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Bukti Perbaikan (Foto / PDF)</label>
                                <input type="file" name="proof_photo" id="proof_photo" accept="image/*,.pdf" class="mt-1 block w-full">
                                @error('proof_photo')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <button type="submit" class="px-4 py-2 bg-sidebar text-white hover:bg-orange-500 border border-transparent rounded transition-colors ">Selesai Dikerjakan</button>
                        </form>
                    @endif
                </div>
            @endcan

            {{-- Panel user pembuat --}}
            @can('close', $ticket)
                <div class="bg-white overflow-hidden shadow-[0_8px_30px_rgb(0,0,0,0.04)] sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium mb-4">Konfirmasi Penyelesaian</h3>
                    <p class="text-sm text-gray-600 mb-4">Pastikan perbaikan sudah sesuai sebelum menutup tiket ini.</p>
                    <form method="POST" action="{{ route('tickets.close', $ticket) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Tutup Tiket</button>
                    </form>
                </div>
            @endcan

            @can('cancel', $ticket)
                <div class="bg-white overflow-hidden shadow-[0_8px_30px_rgb(0,0,0,0.04)] sm:rounded-lg p-6">
                    <form method="POST" action="{{ route('tickets.cancel', $ticket) }}" onsubmit="return confirm('Batalkan tiket ini?')">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="text-red-600 hover:underline text-sm">Batalkan Tiket</button>
                    </form>
                </div>
            @endcan

            {{-- Riwayat --}}
            <div class="bg-white overflow-hidden shadow-[0_8px_30px_rgb(0,0,0,0.04)] sm:rounded-lg p-6">
                <h3 class="text-lg font-medium mb-4">Riwayat Status</h3>
                <ol class="space-y-3">
                    @forelse ($ticket->histories()->latest('created_at')->get() as $history)
                        <li class="text-sm border-l-2 border-cyan-200 pl-3">
                            <span class="font-medium">{{ $history->actor->name }}</span>
                            mengubah status dari
                            <span class="font-medium">{{ $history->status_from ?? 'baru dibuat' }}</span>
                            ke
                            <span class="font-medium">{{ $history->status_to }}</span>
                            <span class="text-gray-400 block">{{ $history->created_at->format('d M Y H:i') }}</span>
                        </li>
                    @empty
                        <li class="text-sm text-gray-500">Belum ada riwayat.</li>
                    @endforelse
                </ol>
            </div>

        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('proof_photo');
        if (!fileInput) return; // Jika form upload bukti tidak ada, hentikan
        
        const form = fileInput.closest('form');
        form.addEventListener('submit', function(e) {
            if (!fileInput.files || fileInput.files.length === 0) return;
            
            const file = fileInput.files[0];
            if (file.size < 1024 * 1024) return;
            
            e.preventDefault();
            const submitBtn = form.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerText = 'Mengompresi & Mengirim...';
            
            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = function(event) {
                const img = new Image();
                img.src = event.target.result;
                img.onload = function() {
                    const canvas = document.createElement('canvas');
                    const MAX_WIDTH = 1200;
                    let width = img.width;
                    let height = img.height;
                    
                    if (width > MAX_WIDTH) {
                        height = Math.round((height * MAX_WIDTH) / width);
                        width = MAX_WIDTH;
                    }
                    
                    canvas.width = width;
                    canvas.height = height;
                    
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, width, height);
                    
                    canvas.toBlob(function(blob) {
                        const compressedFile = new File([blob], file.name, {
                            type: 'image/jpeg',
                            lastModified: Date.now()
                        });
                        
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(compressedFile);
                        fileInput.files = dataTransfer.files;
                        
                        form.submit();
                    }, 'image/jpeg', 0.8);
                };
            };
        });
    });
    </script>
</x-app-layout>