@php
    function getGreeting(): string
    {
        $hour = now()->hour;

        return match (true) {
            $hour < 11 => 'Selamat pagi',
            $hour < 15 => 'Selamat siang',
            $hour < 18 => 'Selamat sore',
            default => 'Selamat malam',
        };
    }
    $user = filament()->auth()->user();
@endphp
<x-filament::widget>
    <x-filament::card
        class="p-6 bg-gradient-to-r from-emerald-100 via-orange-100 to-white dark:from-emerald-900 dark:via-orange-900 dark:to-gray-900">
        <div class="flex flex-col sm:flex-row items-center sm:items-start justify-between gap-6">

            {{-- Kiri: Avatar & Info --}}
            <div class="flex items-center gap-4">
                <img src="{{ $user->getFilamentAvatarUrl() }}" alt="Avatar"
                    class="w-16 h-16 rounded-full ring-2 ring-emerald-500 object-cover">
                <div>
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white">
                        {{ getGreeting() }}, {{ filament()->getUserName($user) }}! 👋
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-300 text-justify leading-relaxed mt-2">
                        Senang melihatmu kembali di Sistem Manajemen Pengolahan dan Pembayaran ISP PT. Tahta Pratama
                        Solusindo. Ayo, mari kita bantu lebih banyak orang hari ini.
                    </p>
                </div>
            </div>

            {{-- Kanan: Logout --}}
            <form action="{{ filament()->getLogoutUrl() }}" method="post" class="my-auto">
                @csrf

                <div class="w-full sm:w-auto block">
                    <x-filament::button type="submit" tag="button" color="danger"
                        icon="heroicon-m-arrow-left-on-rectangle" icon-alias="panels::widgets.account.logout-button"
                        class="w-full justify-center sm:w-auto">
                        Logout
                    </x-filament::button>
                </div>

            </form>
        </div>
    </x-filament::card>
</x-filament::widget>
