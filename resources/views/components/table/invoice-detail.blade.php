<div class="text-sm text-gray-700">
    <div>
        <span class="font-semibold text-gray-600">Kode Langganan : </span>
        <a href="{{ $url }}" class="text-primary-600 hover:underline font-medium">
            #{{ $langgananId }}
        </a>
    </div>

    <div class="flex items-center justify-between">
        <span class="font-semibold text-gray-600">Total Tagihan : </span>
        <span class="text-right font-medium text-rose-600">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</span>
    </div>

    <div class="flex items-center justify-between">
        <span class="font-semibold text-gray-600">Sisa Tagihan : </span>
        <span class="text-right font-medium text-orange-600">Rp {{ number_format($sisaTagihan, 0, ',', '.') }}</span>
    </div>

    <div class="flex items-center justify-between">
        <span class="font-semibold text-gray-600">Jatuh Tempo : </span>
        <span
            class="text-right font-medium {{ \Carbon\Carbon::parse($jatuhTempo)->isPast() ? 'text-red-500' : 'text-emerald-600' }}">
            {{ \Carbon\Carbon::parse($jatuhTempo)->translatedFormat('d F Y') }}
        </span>
    </div>
</div>
