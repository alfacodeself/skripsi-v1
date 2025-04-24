<div class="space-y-6">
    @livewire('App\Livewire\Widget\Langganan\TagihanWidget', ['langganan' => $langganan])
    <x-filament-tables::container>
        {{ $this->table }}
    </x-filament-tables::container>
</div>

<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.clientKey') }}">
</script>
