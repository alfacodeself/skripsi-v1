<?php

namespace App\Filament\Widgets;

use App\Models\Langganan;
use Filament\Widgets\Widget;

class AlamatLanggananWidget extends Widget
{
    protected static string $view = 'filament.widgets.alamat-langganan-widget';
    protected static ?int $sort = 6;
    protected int|string|array $columnSpan = 'full';
    protected static bool $isLazy = false;

    public ?array $langganans;
    public ?array $tileLayers;
    public ?string $defaultLatitude;
    public ?string $defaultLongitude;
    public ?int $defaultZoomLevel;
    public ?int $maxZoomLevel;

    public function mount()
    {
        $this->langganans = Langganan::with(['paket.kategori', 'pelanggan'])->get()->map(function ($item) {
            return [
                'kode' => $item->kode_layanan,
                'paket' => $item->paket->nama . ' - ' . $item->paket->kategori->nama,
                'pelanggan' => $item->pelanggan->nama . ' (' . $item->pelanggan->telepon . ')',
                'status' => $item->status,
                'latitude' => $item->latitude,
                'longitude' => $item->longitude,
                'alamat' => $item->alamat_lengkap,
            ];
        })->toArray();

            $this->tileLayers = [
                [
                    'name' => "GoogleSatellite",
                    'url' => 'http://{s}.google.com/vt?lyrs=s&x={x}&y={y}&z={z}',
                    'subdomains' => ['mt0', 'mt1', 'mt2', 'mt3'],
                ],
                [
                    'name' => "GoogleStreet",
                    'url' => 'http://{s}.google.com/vt?lyrs=m&x={x}&y={y}&z={z}',
                    'subdomains' => ['mt0', 'mt1', 'mt2', 'mt3'],
                ],
                [
                    'name' => "GoogleHybrid",
                    'url' => 'http://{s}.google.com/vt?lyrs=m&x={x}&y={y}&z={z}',
                    'subdomains' => ['mt0', 'mt1', 'mt2', 'mt3'],
                ],
                [
                    'name' => "GoogleTerrain",
                    'url' => 'http://{s}.google.com/vt?lyrs=p&x={x}&y={y}&z={z}',
                    'subdomains' => ['mt0', 'mt1', 'mt2', 'mt3'],
                ],
                [
                    'name' => "OpenStreetMap",
                    'url' => 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                ],
            ];

        $this->defaultLatitude = config('leaflet.latitude');
        $this->defaultLongitude = config('leaflet.longitude');
        $this->defaultZoomLevel = 15;
        $this->maxZoomLevel = config('leaflet.max_zoom_level');
    }
}
