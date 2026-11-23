<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Ubah Tarif</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                @include('admin.fare-settings._form', [
                    'fareSetting' => $fareSetting,
                    'action' => route('admin.fare-settings.update', $fareSetting),
                    'method' => 'PUT',
                ])
            </div>
        </div>
    </div>
</x-app-layout>
