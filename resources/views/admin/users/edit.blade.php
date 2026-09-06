<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.users.index') }}" class="text-gray-500 hover:text-gray-700 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Pengguna') }}: {{ $user->name }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-[0_8px_30px_rgb(0,0,0,0.04)] sm:rounded-xl border border-gray-100">
                <div class="p-6 sm:p-8">
                    <form method="POST" action="{{ route('admin.users.update', $user) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- NIK -->
                            <div>
                                <label for="nik" class="block text-sm font-medium text-gray-700 mb-1">NIK / NPK</label>
                                <input id="nik" class="block w-full border-gray-300 focus:border-brand focus:ring-brand rounded-md shadow-[0_8px_30px_rgb(0,0,0,0.04)] bg-gray-50 text-gray-500 @error('nik') border-red-500 @enderror" type="text" name="nik" value="{{ old('nik', $user->nik) }}" required readonly title="NIK / NPK tidak bisa diubah begitu saja" />
                                <x-input-error :messages="$errors->get('nik')" class="mt-2" />
                            </div>

                            <!-- Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                                <input id="name" class="block w-full border-gray-300 focus:border-brand focus:ring-brand rounded-md shadow-[0_8px_30px_rgb(0,0,0,0.04)] @error('name') border-red-500 @enderror" type="text" name="name" value="{{ old('name', $user->name) }}" required />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <!-- Email -->
                            <div class="md:col-span-2">
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input id="email" class="block w-full border-gray-300 focus:border-brand focus:ring-brand rounded-md shadow-[0_8px_30px_rgb(0,0,0,0.04)] @error('email') border-red-500 @enderror" type="email" name="email" value="{{ old('email', $user->email) }}" required />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            <!-- Role -->
                            <div>
                                <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Peran (Role)</label>
                                @php $currentRole = old('role', $user->roles->first()?->name ?? 'user'); @endphp
                                <select id="role" name="role" class="block w-full border-gray-300 focus:border-brand focus:ring-brand rounded-md shadow-[0_8px_30px_rgb(0,0,0,0.04)]" required>
                                    <option value="user" {{ $currentRole == 'user' ? 'selected' : '' }}>User (Karyawan)</option>
                                    <option value="operator" {{ $currentRole == 'operator' ? 'selected' : '' }}>Operator</option>
                                    <option value="admin" {{ $currentRole == 'admin' ? 'selected' : '' }}>Administrator</option>
                                </select>
                                <x-input-error :messages="$errors->get('role')" class="mt-2" />
                            </div>

                            <!-- Division -->
                            <div>
                                <label for="work_unit_id" class="block text-sm font-medium text-gray-700 mb-1">Unit Kerja (Opsional)</label>
                                <select id="work_unit_id" name="work_unit_id" class="block w-full border-gray-300 focus:border-brand focus:ring-brand rounded-md shadow-[0_8px_30px_rgb(0,0,0,0.04)]">
                                    <option value="">-- Tanpa Unit Kerja --</option>
                                    @foreach($workUnits as $workUnit)
                                        <option value="{{ $workUnit->id }}" {{ old('work_unit_id', $user->work_unit_id) == $workUnit->id ? 'selected' : '' }}>
                                            {{ $workUnit->name }} ({{ $workUnit->department->name ?? '-' }} / {{ $workUnit->department->compartment->name ?? '-' }})
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('work_unit_id')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mt-8 border-t border-gray-100 pt-6">
                            <h4 class="text-sm font-bold text-gray-800 mb-4">Ubah Password (Opsional)</h4>
                            <p class="text-xs text-gray-500 mb-4">Biarkan kosong jika tidak ingin mengubah password.</p>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Password -->
                                <div>
                                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                                    <input id="password" class="block w-full border-gray-300 focus:border-brand focus:ring-brand rounded-md shadow-[0_8px_30px_rgb(0,0,0,0.04)] @error('password') border-red-500 @enderror" type="password" name="password" autocomplete="new-password" />
                                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                </div>

                                <!-- Confirm Password -->
                                <div>
                                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
                                    <input id="password_confirmation" class="block w-full border-gray-300 focus:border-brand focus:ring-brand rounded-md shadow-[0_8px_30px_rgb(0,0,0,0.04)]" type="password" name="password_confirmation" autocomplete="new-password" />
                                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-8 gap-4 pt-6 border-t border-gray-100">
                            <a href="{{ route('admin.users.index') }}" class="text-gray-500 hover:text-gray-700 font-medium text-sm transition-colors">
                                Batal
                            </a>
                            <button type="submit" class="px-6 py-2.5 bg-sidebar text-white hover:bg-orange-500 transition-colors border border-transparent text-white rounded-lg  hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 font-medium">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

