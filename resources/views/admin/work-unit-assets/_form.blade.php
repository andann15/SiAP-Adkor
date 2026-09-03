@php
    $asset = $asset ?? null;
@endphp

<div class="grid grid-cols-2 gap-4">
    <div class="mb-4 col-span-2">
        <label for="code" class="block text-sm font-medium text-gray-700">
            Nomor BAPBS/BAPBSAT
        </label>
        <input type="text" name="code" id="code" value="{{ old('code', $asset->code ?? '') }}"
               class="mt-1 block w-full border-gray-300 rounded-md shadow-[0_8px_30px_rgb(0,0,0,0.04)]">
        @error('code')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-4 col-span-2">
        <label for="name" class="block text-sm font-medium text-gray-700">Unit Kerja / Departemen / Kompartemen</label>
        <input type="text" name="name" id="name" value="{{ old('name', $asset->name ?? '') }}"
               placeholder="Contoh: Unit Shift A / Departemen Operasi / Kompartemen Produksi"
               class="mt-1 block w-full border-gray-300 rounded-md shadow-[0_8px_30px_rgb(0,0,0,0.04)]">
        @error('name')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-4">
        <label for="asset_category_id" class="block text-sm font-medium text-gray-700">Kategori</label>
        <select name="asset_category_id" id="asset_category_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-[0_8px_30px_rgb(0,0,0,0.04)]">
            <option value="">-- Pilih Kategori --</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(old('asset_category_id', $asset->asset_category_id ?? '') == $category->id)>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
        @error('asset_category_id')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-4">
        <label for="brand_id" class="block text-sm font-medium text-gray-700">Merek (opsional)</label>
        <select name="brand_id" id="brand_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-[0_8px_30px_rgb(0,0,0,0.04)]">
            <option value="">-- Pilih Merek --</option>
            @foreach ($brands as $brand)
                <option value="{{ $brand->id }}" @selected(old('brand_id', $asset->brand_id ?? '') == $brand->id)>
                    {{ $brand->name }}
                </option>
            @endforeach
        </select>
        @error('brand_id')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-4">
        <label for="model" class="block text-sm font-medium text-gray-700">Jenis Limbah</label>
        <select name="model" id="model" class="mt-1 block w-full border-gray-300 rounded-md shadow-[0_8px_30px_rgb(0,0,0,0.04)]">
            <option value="">-- Pilih Jenis Limbah --</option>
            <option value="Limbah" @selected(old('model', $asset->model ?? '') === 'Limbah')>Limbah</option>
            <option value="Non Limbah" @selected(old('model', $asset->model ?? '') === 'Non Limbah')>Non Limbah</option>
        </select>
        @error('model')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-4">
        <label for="serial_number" class="block text-sm font-medium text-gray-700">Nomor Seri (opsional)</label>
        <input type="text" name="serial_number" id="serial_number" value="{{ old('serial_number', $asset->serial_number ?? '') }}"
               class="mt-1 block w-full border-gray-300 rounded-md shadow-[0_8px_30px_rgb(0,0,0,0.04)]">
        @error('serial_number')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-4">
        <label for="purchase_date" class="block text-sm font-medium text-gray-700">Tanggal Pembelian (opsional)</label>
        <input type="date" name="purchase_date" id="purchase_date"
               value="{{ old('purchase_date', isset($asset->purchase_date) ? $asset->purchase_date->format('Y-m-d') : '') }}"
               class="mt-1 block w-full border-gray-300 rounded-md shadow-[0_8px_30px_rgb(0,0,0,0.04)]">
        @error('purchase_date')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-4">
        <label for="warranty_end" class="block text-sm font-medium text-gray-700">Garansi Hingga (opsional)</label>
        <input type="date" name="warranty_end" id="warranty_end"
               value="{{ old('warranty_end', isset($asset->warranty_end) ? $asset->warranty_end->format('Y-m-d') : '') }}"
               class="mt-1 block w-full border-gray-300 rounded-md shadow-[0_8px_30px_rgb(0,0,0,0.04)]">
        @error('warranty_end')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-4">
        <label for="location_id" class="block text-sm font-medium text-gray-700">Lokasi Penempatan</label>
        <select name="location_id" id="location_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-[0_8px_30px_rgb(0,0,0,0.04)]">
            <option value="">-- Pilih Lokasi --</option>
            @foreach ($locations as $location)
                <option value="{{ $location->id }}" @selected(old('location_id', $asset->location_id ?? '') == $location->id)>
                    {{ $location->name }}
                </option>
            @endforeach
        </select>
        @error('location_id')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-4">
        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
        <select name="status" id="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-[0_8px_30px_rgb(0,0,0,0.04)]">
            <option value="">-- Pilih Status --</option>
            @foreach ($statuses as $status)
                <option value="{{ $status->slug }}" @selected(old('status', $asset->status ?? '') == $status->slug)>
                    {{ $status->name }}
                </option>
            @endforeach
        </select>
        @error('status')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-4 col-span-2">
        <label for="work_unit_id" class="block text-sm font-medium text-gray-700">Penugasan Ke Unit Kerja</label>
        <select name="work_unit_id" id="work_unit_id"
                class="ts-select mt-1 block w-full border-gray-300 rounded-md shadow-[0_8px_30px_rgb(0,0,0,0.04)]"
                data-placeholder="-- Cari & Pilih Unit Kerja --">
            <option value=""></option>
            @foreach ($workUnits as $wu)
                @php
                    $deptName = $wu->department?->name;
                    $compName = $wu->department?->compartment?->name;
                    // Build a descriptive label: prioritize lower levels first
                    $parts = array_filter([$compName, $deptName, $wu->name]);
                    $label = implode(' › ', $parts) ?: '(tanpa nama)';
                @endphp
                <option value="{{ $wu->id }}" @selected(old('work_unit_id', $asset->work_unit_id ?? '') == $wu->id)>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        @error('work_unit_id')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-4 col-span-2">
        <label for="notes" class="block text-sm font-medium text-gray-700">Rincian / Keterangan Khusus</label>
        <textarea name="notes" id="notes" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-[0_8px_30px_rgb(0,0,0,0.04)]">{{ old('notes', $asset->notes ?? '') }}</textarea>
        <p class="text-xs text-gray-500 mt-1">Gunakan titik koma (;) atau enter untuk memisahkan daftar barang agar rapi.</p>
        @error('notes')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
    </div>
</div>
