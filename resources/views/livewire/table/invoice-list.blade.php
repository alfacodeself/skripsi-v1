<x-filament-tables::container>
    {{ $this->table }}
</x-filament-tables::container>

<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.clientKey') }}">
</script>
