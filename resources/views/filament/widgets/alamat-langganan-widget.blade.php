<x-filament-widgets::widget>
    <x-filament::section>
        <div id="map" style="height: 400px;"></div>
    </x-filament::section>
</x-filament-widgets::widget>

@push('styles')
    <link rel="stylesheet" href="{{ asset('libs/leaflet/leaflet.css') }}">
    <style>
        #map {
            z-index: 0 !important;
            position: relative;
            /* pastikan bisa ditumpuk */
        }

        /* Pastikan pane utama Leaflet tidak mengganggu z-index lainnya */
        .leaflet-map-pane,
        .leaflet-tile-pane {
            z-index: 0 !important;
        }

        .info.legend {
            background-color: rgba(255, 255, 255, 0.8);
            /* Putih dengan sedikit transparansi */
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 12px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
        }

        .info.legend i {
            margin-right: 5px;
        }

        .info.legend div {
            padding-left: 15px;
        }

        /* Menambahkan warna hitam untuk tulisan pada legend */
        .info.legend span {
            color: #000;
            /* Mengubah teks menjadi hitam */
            font-weight: normal;
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('libs/leaflet/leaflet.js') }}"></script>
    <script>
        document.addEventListener('livewire:navigated', function() {
            const mapContainer = document.getElementById('map');

            // Jika map container tidak ditemukan, hentikan
            if (!mapContainer) {
                console.log('Map container tidak ditemukan, tidak menjalankan init.');
                return;
            }

            // Jika map sudah pernah diinit, hindari duplikasi
            if (mapContainer.dataset.initialized === "true") {
                console.log('Map sudah diinisialisasi sebelumnya.');
                return;
            }

            mapContainer.dataset.initialized = "true";

            console.log('Inisialisasi map dimulai');

            // --- Lanjutkan inisialisasi map seperti sebelumnya ---
            const defaultLat = {{ $this->defaultLatitude }};
            const defaultLng = {{ $this->defaultLongitude }};
            const defaultZoom = {{ $this->defaultZoomLevel }};
            const maxZoom = {{ $this->maxZoomLevel }};
            const tileLayers = @json($this->tileLayers);
            const langganans = @json($langganans);

            const map = L.map('map').setView([defaultLat, defaultLng], defaultZoom);
            let layers = {};

            tileLayers.forEach(layer => {
                const tileLayerOptions = {
                    maxZoom
                };
                if (layer.subdomains) tileLayerOptions.subdomains = layer.subdomains;
                layers[layer.name] = L.tileLayer(layer.url, tileLayerOptions);
            });

            const defaultLayer = Object.values(layers)[0];
            if (defaultLayer) defaultLayer.addTo(map);

            L.control.layers(layers, {}, {
                collapsed: true,
                position: 'topright'
            }).addTo(map);

            const statusColors = {
                'aktif': '#39FF14',
                'expired': '#FF073A',
                'pending': '#FF8C00',
                'tagihan': '#007BFF',
            };

            langganans.forEach(function(item) {
                const color = statusColors[item.status] || '#808080';
                const popupContent = [
                    "<b>Kode:</b> " + item.kode,
                    "<b>Paket:</b> " + item.paket,
                    "<b>Pelanggan:</b> " + item.pelanggan,
                    "<b>Status:</b> " + item.status,
                    "<b>Alamat:</b> " + item.alamat,
                ].join("<br>");

                if (item.latitude && item.longitude) {
                    L.circleMarker([item.latitude, item.longitude], {
                        radius: 12,
                        fillColor: color,
                        color: color,
                        weight: 2,
                        opacity: 1,
                        fillOpacity: 1
                    }).addTo(map).bindPopup(popupContent);
                }
            });

            const legend = L.control({
                position: 'topleft'
            });
            legend.onAdd = function() {
                const div = L.DomUtil.create('div', 'info legend');
                const labels = [];
                const categories = ['aktif', 'expired', 'pending', 'tagihan'];

                categories.forEach(function(status) {
                    const color = statusColors[status];
                    labels.push(
                        `<i style="background-color:${color}; width: 15px; height: 15px; border-radius: 50%; display: inline-block;"></i>` +
                        `<span>${status.charAt(0).toUpperCase() + status.slice(1)}</span>`
                    );
                });

                div.innerHTML = labels.join('<br>');
                return div;
            };
            legend.addTo(map);
        });
    </script>
@endpush
