<div class="text-sm text-gray-700">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
            <x-heroicon-m-check-circle class="w-5 h-5 {{ $statusAngsuran ? 'text-emerald-600' : 'text-gray-400' }}" />
            <span class="font-semibold text-gray-600">Status Angsuran</span>
        </div>
        <span class="font-medium">
            {{ $statusAngsuran ? 'Ya' : 'Tidak' }}
        </span>
    </div>

    @if ($statusAngsuran && $jumlahAngsuran)
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <x-heroicon-m-currency-dollar class="w-5 h-5 text-blue-500" />
                <span class="font-semibold text-gray-600">Jumlah per Angsuran</span>
            </div>
            <span class="font-medium text-right text-blue-600">
                Rp {{ number_format($jumlahAngsuran, 0, ',', '.') }}
            </span>
        </div>
    @endif
</div>
