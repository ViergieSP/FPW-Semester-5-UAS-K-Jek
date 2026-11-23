@php
    /** @var \App\Models\User $user */
@endphp

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)"
                required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification"
                            class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        @if ($user instanceof \App\Models\User && $user->isDriver())
            @php
                $hasActiveOrder = $user->hasActiveOrder();
            @endphp

            @if ($hasActiveOrder)
                <div role="alert" class="rounded-md border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                    Data kendaraan terkunci sementara karena Anda memiliki pesanan yang sedang berjalan. Selesaikan pesanan
                    terlebih dahulu untuk mengubah data kendaraan.
                </div>
            @endif

            <div>
                <x-input-label for="vehicle_name" :value="__('Nama Kendaraan')" />
                <x-text-input id="vehicle_name" name="vehicle_name" type="text"
                    class="mt-1 block w-full disabled:cursor-not-allowed disabled:bg-gray-100 disabled:text-gray-500"
                    :value="old('vehicle_name', $user->vehicle_name)" placeholder="Contoh: Honda Vario 125"
                    :disabled="$hasActiveOrder" :required="!$hasActiveOrder" />
                <x-input-error class="mt-2" :messages="$errors->get('vehicle_name')" />
            </div>

            <div>
                <x-input-label for="license_plate" :value="__('Plat Nomor Kendaraan')" />
                <x-text-input id="license_plate" name="license_plate" type="text"
                    class="mt-1 block w-full uppercase disabled:cursor-not-allowed disabled:bg-gray-100 disabled:text-gray-500"
                    :value="old('license_plate', $user->license_plate)" placeholder="Contoh: T 1234 AB"
                    :disabled="$hasActiveOrder" :required="!$hasActiveOrder" />
                <x-input-error class="mt-2" :messages="$errors->get('license_plate')" />
            </div>
        @endif

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600">{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>