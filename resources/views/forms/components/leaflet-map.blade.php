<!-- Memuat CSS Leaflet dari direktori public -->
<link rel="stylesheet" href="{{ asset('libs/leaflet/leaflet.css') }}">

<!-- Menggunakan komponen dinamis untuk membungkus field peta -->
<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
    lazy
>
    <!-- Container utama peta dengan Alpine.js data binding -->
    <div
        x-data="{
            // Menghubungkan koordinat dengan state Livewire
            latitude: $wire.entangle('{{ $getStatePath() }}.latitude').live,
            longitude: $wire.entangle('{{ $getStatePath() }}.longitude').live,
            // Mengatur tinggi peta dari atribut tambahan
            height: '{{ $getExtraAttributes()['height'] }}px',
            // Variabel untuk menyimpan instance peta dan marker
            map: null,
            marker: null,
            // Mendapatkan konfigurasi tile layers dari atribut tambahan
            tileLayers: @js($getExtraAttributes()['tileLayers']),
            maxZoomLevel: {{ $getExtraAttributes()['maxZoomLevel'] }},

            // Method untuk inisialisasi peta
            initMap() {
                // Mengatur tinggi container peta
                this.$el.style.height = this.height;

                // Membuat instance peta Leaflet
                this.map = L.map(this.$el, {
                    zoomControl: true,
                    zoomControlOptions: {
                        position: 'topright'
                    }
                }).setView([this.latitude, this.longitude], 15);

                // Menginisialisasi layer-layer peta
                let layers = {};
                this.tileLayers.forEach(layer => {
                    const tileLayerOptions = {
                        maxZoom: this.maxZoomLevel
                    };
                    if (layer.subdomains) {
                        tileLayerOptions.subdomains = layer.subdomains;
                    }
                    layers[layer.name] = L.tileLayer(layer.url, tileLayerOptions);
                });

                // Menambahkan layer default ke peta
                const defaultLayer = Object.values(layers)[0];
                if (defaultLayer) defaultLayer.addTo(this.map);

                // Menambahkan kontrol layer ke peta
                L.control.layers(layers, {}, {
                    collapsed: true,
                    position: 'topright'
                }).addTo(this.map);

                // Menambahkan marker yang bisa di-drag
                this.marker = L.marker([this.latitude, this.longitude], {draggable: true}).addTo(this.map);

                // Event handler saat marker selesai di-drag
                this.marker.on('dragend', () => {
                    const position = this.marker.getLatLng();
                    this.latitude = parseFloat(position.lat.toFixed(6));
                    this.longitude = parseFloat(position.lng.toFixed(6));
                });

                // Event handler saat peta diklik
                this.map.on('click', (e) => {
                    this.latitude = parseFloat(e.latlng.lat.toFixed(6));
                    this.longitude = parseFloat(e.latlng.lng.toFixed(6));
                    this.marker.setLatLng([this.latitude, this.longitude]);
                });

                // Memantau perubahan koordinat dan memperbarui posisi marker
                $watch('latitude', (value) => this.marker.setLatLng([value, this.longitude]));
                $watch('longitude', (value) => this.marker.setLatLng([this.latitude, value]));
            }
        }"
        x-init="initMap"
        wire:ignore
        wire:key="{{ $field->getId() }}"
        style="position: relative; z-index: 0;"
        class="w-full rounded-lg shadow">
        <!-- Area interaksi dengan state di Alpine.js -->
    </div>

</x-dynamic-component>

<!-- Memuat JavaScript Leaflet dari direktori public -->
<script src="{{ asset('libs/leaflet/leaflet.js') }}"></script>
