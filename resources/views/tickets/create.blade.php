<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('tickets.index') }}" class="text-gray-500 hover:text-gray-700 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Buat Tiket Baru') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-[0_8px_30px_rgb(0,0,0,0.04)] sm:rounded-lg p-6">
                <form method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data" id="ticket-form">
                    @csrf

                    <div class="mb-4">
                        <label for="asset_id" class="block text-sm font-medium text-gray-700">Aset</label>
                        <select name="asset_id" id="asset_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-brand focus:ring-brand">
                            <option value="">Pilih aset</option>
                            @if(isset($myAssets) && $myAssets->isNotEmpty())
                                <optgroup label="Aset Anda">
                                    @foreach($myAssets as $asset)
                                        <option value="{{ $asset->id }}" {{ (old('asset_id') ?? ($preselectedAssetId ?? '')) == $asset->id ? 'selected' : '' }}>
                                            {{ $asset->code }} - {{ $asset->name }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif
                            
                            @if(isset($otherAssets) && $otherAssets->isNotEmpty())
                                <optgroup label="Aset Lainnya">
                                    @foreach($otherAssets as $asset)
                                        <option value="{{ $asset->id }}" {{ (old('asset_id') ?? ($preselectedAssetId ?? '')) == $asset->id ? 'selected' : '' }}>
                                            {{ $asset->code }} - {{ $asset->name }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @elseif(isset($assets))
                                @foreach($assets as $asset)
                                    <option value="{{ $asset->id }}" {{ old('asset_id') == $asset->id ? 'selected' : '' }}>
                                        {{ $asset->code }} - {{ $asset->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        @error('asset_id')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi Masalah</label>
                        <textarea name="description" id="description" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-[0_8px_30px_rgb(0,0,0,0.04)]">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Foto Kondisi Aset -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Foto Kondisi Aset</label>

                        <!-- Tombol pilihan -->
                        <div class="flex gap-2 mb-3">
                            <button type="button" id="btn-upload"
                                class="flex-1 flex items-center justify-center gap-2 px-3 py-2 rounded-lg border-2 border-sidebar bg-sidebar text-white text-sm font-medium hover:bg-orange-500 hover:border-orange-500 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                Upload Foto
                            </button>
                            <button type="button" id="btn-camera"
                                class="flex-1 flex items-center justify-center gap-2 px-3 py-2 rounded-lg border-2 border-gray-300 bg-white text-gray-700 text-sm font-medium hover:border-orange-500 hover:text-orange-500 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                Buka Kamera
                            </button>
                        </div>

                        <!-- Input file biasa (upload) -->
                        <div id="upload-section">
                            <input type="file" name="photo" id="photo" accept="image/*,.pdf" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-sidebar file:text-white hover:file:bg-orange-500 transition-colors">
                        </div>

                        <!-- Kamera Section -->
                        <div id="camera-section" class="hidden">
                            <div class="rounded-xl overflow-hidden border border-gray-200 bg-black relative">
                                <video id="camera-preview" class="w-full max-h-64 object-cover" autoplay playsinline></video>
                                <div id="camera-overlay" class="hidden absolute inset-0 bg-black flex items-center justify-center">
                                    <img id="captured-preview" class="max-h-64 object-contain" src="" alt="Foto yang diambil">
                                </div>
                            </div>
                            <div class="flex gap-2 mt-2">
                                <button type="button" id="btn-capture"
                                    class="flex-1 flex items-center justify-center gap-2 px-4 py-2 bg-sidebar text-white rounded-lg text-sm font-medium hover:bg-orange-500 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path></svg>
                                    Ambil Foto
                                </button>
                                <button type="button" id="btn-retake" class="hidden flex-1 flex items-center justify-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:border-orange-500 hover:text-orange-500 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                    Ulangi
                                </button>
                            </div>
                        </div>

                        <!-- Preview foto yang dipilih (dari upload) -->
                        <div id="upload-preview-wrap" class="hidden mt-2">
                            <img id="upload-preview" class="rounded-lg max-h-48 object-contain border" src="" alt="Preview">
                        </div>

                        <!-- Canvas tersembunyi untuk proses foto kamera -->
                        <canvas id="photo-canvas" class="hidden"></canvas>

                        @error('photo')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end gap-2">
                        <a href="{{ route('tickets.index') }}" class="px-4 py-2 border rounded">Batal</a>
                        <button type="submit" id="submit-btn" class="px-4 py-2 bg-sidebar text-white hover:bg-orange-500 border border-transparent rounded transition-colors">Kirim Tiket</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('ticket-form');
        const btnUpload = document.getElementById('btn-upload');
        const btnCamera = document.getElementById('btn-camera');
        const uploadSection = document.getElementById('upload-section');
        const cameraSection = document.getElementById('camera-section');
        const photoInput = document.getElementById('photo');
        const uploadPreviewWrap = document.getElementById('upload-preview-wrap');
        const uploadPreview = document.getElementById('upload-preview');
        const cameraPreview = document.getElementById('camera-preview');
        const cameraOverlay = document.getElementById('camera-overlay');
        const capturedPreview = document.getElementById('captured-preview');
        const canvas = document.getElementById('photo-canvas');
        const btnCapture = document.getElementById('btn-capture');
        const btnRetake = document.getElementById('btn-retake');
        const submitBtn = document.getElementById('submit-btn');

        let stream = null;
        let capturedBlob = null;
        let mode = 'upload'; // 'upload' or 'camera'

        // --- Tombol Upload ---
        btnUpload.addEventListener('click', function () {
            mode = 'upload';
            stopCamera();
            uploadSection.classList.remove('hidden');
            cameraSection.classList.add('hidden');
            btnUpload.classList.add('bg-sidebar', 'text-white', 'border-sidebar');
            btnUpload.classList.remove('bg-white', 'text-gray-700', 'border-gray-300');
            btnCamera.classList.remove('border-orange-500', 'text-orange-500');
            btnCamera.classList.add('border-gray-300', 'text-gray-700', 'bg-white');
            capturedBlob = null;
        });

        // --- Tombol Kamera ---
        btnCamera.addEventListener('click', async function () {
            mode = 'camera';
            uploadSection.classList.add('hidden');
            uploadPreviewWrap.classList.add('hidden');
            cameraSection.classList.remove('hidden');
            btnCamera.classList.add('border-orange-500', 'text-orange-500');
            btnCamera.classList.remove('border-gray-300', 'text-gray-700');
            btnUpload.classList.remove('bg-sidebar', 'text-white', 'border-sidebar');
            btnUpload.classList.add('bg-white', 'text-gray-700', 'border-gray-300');
            capturedBlob = null;
            cameraOverlay.classList.add('hidden');
            btnCapture.classList.remove('hidden');
            btnRetake.classList.add('hidden');

            try {
                stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' }, audio: false });
                cameraPreview.srcObject = stream;
            } catch (err) {
                alert('Kamera tidak dapat diakses: ' + err.message + '\n\nPastikan browser mendapat izin kamera.');
                btnUpload.click();
            }
        });

        // --- Ambil Foto ---
        btnCapture.addEventListener('click', function () {
            if (!stream) return;
            const ctx = canvas.getContext('2d');
            canvas.width = cameraPreview.videoWidth;
            canvas.height = cameraPreview.videoHeight;
            ctx.drawImage(cameraPreview, 0, 0);
            canvas.toBlob(function (blob) {
                capturedBlob = blob;
                const url = URL.createObjectURL(blob);
                capturedPreview.src = url;
                cameraOverlay.classList.remove('hidden');
                btnCapture.classList.add('hidden');
                btnRetake.classList.remove('hidden');
            }, 'image/jpeg', 0.85);
        });

        // --- Ulangi Foto ---
        btnRetake.addEventListener('click', function () {
            capturedBlob = null;
            cameraOverlay.classList.add('hidden');
            btnCapture.classList.remove('hidden');
            btnRetake.classList.add('hidden');
        });

        // --- Preview saat upload file ---
        photoInput.addEventListener('change', function () {
            const file = this.files[0];
            if (file) {
                const url = URL.createObjectURL(file);
                if (file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf')) {
                    uploadPreviewWrap.innerHTML = `<div class="p-4 border rounded-lg bg-gray-50 flex items-center gap-2"><svg class="w-6 h-6 text-red-500" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 1.5L18.5 9H13V3.5zM6 20V4h5v7h7v9H6z"/></svg><span class="text-sm font-medium text-gray-700">${file.name}</span></div>`;
                } else {
                    uploadPreviewWrap.innerHTML = `<img id="upload-preview" class="rounded-lg max-h-48 object-contain border" src="${url}" alt="Preview">`;
                }
                uploadPreviewWrap.classList.remove('hidden');
            }
        });

        function stopCamera() {
            if (stream) {
                stream.getTracks().forEach(t => t.stop());
                stream = null;
            }
        }

        // --- Submit Form ---
        form.addEventListener('submit', function (e) {
            if (mode === 'camera') {
                if (!capturedBlob) {
                    e.preventDefault();
                    alert('Silakan ambil foto terlebih dahulu sebelum mengirim tiket.');
                    return;
                }
                e.preventDefault();
                submitBtn.disabled = true;
                submitBtn.innerText = 'Mengirim...';
                stopCamera();
                const file = new File([capturedBlob], 'foto-kamera.jpg', { type: 'image/jpeg', lastModified: Date.now() });
                const dt = new DataTransfer();
                dt.items.add(file);
                photoInput.files = dt.files;
                // Buat input hidden agar file tetap terkirim
                form.submit();
                return;
            }

            // Mode upload — kompresi jika > 1MB dan bukan PDF
            const file = photoInput.files[0];
            if (!file) return;
            
            const isPdf = file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf');
            if (isPdf || file.size < 1024 * 1024) return;

            e.preventDefault();
            submitBtn.disabled = true;
            submitBtn.innerText = 'Mengompresi & Mengirim...';

            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = function (event) {
                const img = new Image();
                img.src = event.target.result;
                img.onload = function () {
                    const MAX_WIDTH = 1200;
                    let w = img.width, h = img.height;
                    if (w > MAX_WIDTH) { h = Math.round((h * MAX_WIDTH) / w); w = MAX_WIDTH; }
                    canvas.width = w; canvas.height = h;
                    canvas.getContext('2d').drawImage(img, 0, 0, w, h);
                    canvas.toBlob(function (blob) {
                        const dt = new DataTransfer();
                        dt.items.add(new File([blob], file.name, { type: 'image/jpeg', lastModified: Date.now() }));
                        photoInput.files = dt.files;
                        form.submit();
                    }, 'image/jpeg', 0.8);
                };
            };
        });
    });
    </script>
</x-app-layout>