<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
        @csrf

        <!-- Nama Lengkap -->
        <div>
            <x-input-label for="name" :value="__('Nama Lengkap')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required
                autofocus />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- NPM -->
        <div class="mt-4">
            <x-input-label for="npm" :value="__('NPM (Nomor Pokok Mahasiswa)')" />
            <x-text-input id="npm" class="block mt-1 w-full" type="text" name="npm" :value="old('npm')" required />
            <x-input-error :messages="$errors->get('npm')" class="mt-2" />
        </div>

        <!-- Nomor WhatsApp -->
        <div class="mt-4">
            <x-input-label for="phone_number" :value="__('Nomor WhatsApp Aktif')" />
            <x-text-input id="phone_number" class="block mt-1 w-full" type="text" name="phone_number"
                :value="old('phone_number')" placeholder="Contoh: 081234567890" required />
            <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
        </div>

        <!-- Email -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"
                required />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Daftar Sebagai -->
        <div class="mt-4">
            <x-input-label for="role" :value="__('Daftar Sebagai')" />
            <select id="role" name="role"
                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                required>
                <option value="customer" {{ old('role') == 'customer' ? 'selected' : '' }}>Customer (Penumpang / Pemesan)
                </option>
                <option value="driver" {{ old('role') == 'driver' ? 'selected' : '' }}>Driver (Pengemudi Ojek Mahasiswa)
                </option>
            </select>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <!-- Upload Foto -->
        <div class="mt-4">
            <x-input-label for="profile_photo" :value="__('Upload Foto Profil (JPG/PNG/PDF, Maks. 2MB)')" />
            <input id="profile_photo" type="file" name="profile_photo" accept=".jpg,.jpeg,.png,.pdf"
                class="block mt-1 w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 p-2"
                required />
            <x-input-error :messages="$errors->get('profile_photo')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                name="password_confirmation" required />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md" href="{{ route('login') }}">
                {{ __('Sudah punya akun?') }}
            </a>
            <x-primary-button class="ms-4">
                {{ __('Daftar K-JEK') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>