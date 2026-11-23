<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data"
        x-data="{ role: @js(old('role', 'customer')) }">
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
            <select id="role" name="role" x-model="role"
                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                required>
                <option value="customer" @selected(old('role', 'customer') === 'customer')>
                    Customer (Penumpang / Pemesan)
                </option>
                <option value="driver" @selected(old('role') === 'driver')>
                    Driver (Pengemudi Ojek Mahasiswa)
                </option>
            </select>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <!-- Data Kendaraan Khusus Driver -->
        <div x-show="role === 'driver'" style="{{ old('role') === 'driver' ? '' : 'display: none;' }}"
            class="mt-4 flex flex-col gap-4">
            <!-- Nama Kendaraan -->
            <div>
                <x-input-label for="vehicle_name" :value="__('Nama Kendaraan')" />
                <x-text-input id="vehicle_name" class="block mt-1 w-full" type="text" name="vehicle_name"
                    :value="old('vehicle_name')" placeholder="Contoh: Honda Vario 125 / Yamaha NMAX"
                    x-bind:required="role === 'driver'" x-bind:disabled="role !== 'driver'" />
                <x-input-error :messages="$errors->get('vehicle_name')" class="mt-2" />
            </div>

            <!-- Plat Nomor -->
            <div>
                <x-input-label for="license_plate" :value="__('Plat Nomor Kendaraan')" />
                <x-text-input id="license_plate" class="block mt-1 w-full uppercase" type="text" name="license_plate"
                    :value="old('license_plate')" placeholder="Contoh: T 1234 AB" x-bind:required="role === 'driver'"
                    x-bind:disabled="role !== 'driver'" />
                <x-input-error :messages="$errors->get('license_plate')" class="mt-2" />
            </div>
        </div>

        <!-- Upload Foto -->
        <div class="mt-4" x-data="{
                previewUrl: null,
                fileName: '',
                fileSize: '',
                isImage: false,
                isPdf: false,
                handleFileChange(event) {
                    const file = event.target.files[0];

                    if (this.previewUrl) {
                        URL.revokeObjectURL(this.previewUrl);
                        this.previewUrl = null;
                    }

                    if (! file) {
                        this.fileName = '';
                        this.fileSize = '';
                        this.isImage = false;
                        this.isPdf = false;
                        return;
                    }

                    this.fileName = file.name;
                    this.fileSize = file.size >= 1048576
                        ? (file.size / 1048576).toFixed(2) + ' MB'
                        : Math.ceil(file.size / 1024) + ' KB';
                    this.isImage = file.type.startsWith('image/') || /\.(jpe?g|png)$/i.test(file.name);
                    this.isPdf = file.type === 'application/pdf' || /\.pdf$/i.test(file.name);

                    if (this.isImage) {
                        this.previewUrl = URL.createObjectURL(file);
                    }
                },
                clearFile() {
                    if (this.previewUrl) {
                        URL.revokeObjectURL(this.previewUrl);
                        this.previewUrl = null;
                    }
                    this.$refs.photoInput.value = '';
                    this.fileName = '';
                    this.fileSize = '';
                    this.isImage = false;
                    this.isPdf = false;
                }
            }">
            <x-input-label for="profile_photo" :value="__('Upload Foto Profil (JPG/PNG/PDF, Maks. 2MB)')" />
            <input id="profile_photo" x-ref="photoInput" type="file" name="profile_photo" accept=".jpg,.jpeg,.png,.pdf"
                x-on:change="handleFileChange($event)"
                class="block mt-1 w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 p-2"
                required />

            <!-- Preview Gambar -->
            <div x-show="isImage && previewUrl" style="display: none;"
                class="mt-3 flex flex-col gap-2 rounded-lg border border-gray-200 bg-gray-50 p-3">
                <div class="flex items-center justify-between gap-2 text-xs text-gray-600">
                    <span class="truncate font-medium" x-text="fileName"></span>
                    <div class="flex items-center gap-2 shrink-0">
                        <span x-text="fileSize"></span>
                        <button type="button" x-on:click="clearFile()"
                            class="font-semibold text-red-600 hover:text-red-700">
                            Hapus
                        </button>
                    </div>
                </div>
                <img x-bind:src="previewUrl" alt="Preview foto profil"
                    class="max-h-56 w-full rounded-md border border-gray-200 bg-white object-contain" />
            </div>

            <!-- Info File PDF -->
            <div x-show="isPdf" style="display: none;"
                class="mt-3 flex items-center justify-between gap-3 rounded-lg border border-gray-200 bg-gray-50 p-3 text-xs text-gray-700">
                <div class="flex items-center gap-2 min-w-0">
                    <span class="rounded bg-red-100 px-2 py-0.5 font-bold text-red-700">PDF</span>
                    <span class="truncate font-medium" x-text="fileName"></span>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <span class="text-gray-500" x-text="fileSize"></span>
                    <button type="button" x-on:click="clearFile()"
                        class="font-semibold text-red-600 hover:text-red-700">
                        Hapus
                    </button>
                </div>
            </div>

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