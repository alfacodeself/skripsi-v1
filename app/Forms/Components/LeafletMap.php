<?php

namespace App\Forms\Components;

use Exception;
use Filament\Forms\Components\Field;
use Closure;

class LeafletMap extends Field
{
    protected string $view = 'forms.components.leaflet-map';

    protected ?int $height = 300;
    // protected ?int $maxZoomLevel;
    protected array $tileLayers = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->tileLayers([
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
        ]);
        $this->default([
            'latitude' => config('leaflet.latitude'),
            'longitude' => config('leaflet.longitude')
        ]);

        $this->dehydrateStateUsing(fn($state) => json_encode($state));

        $this->afterStateHydrated(function ($state) {
            if (is_string($state)) {
                $state = json_decode($state, true);
            }

            if (is_array($state) && isset($state['latitude']) && isset($state['longitude'])) {
                return;
            }

            // Jika state adalah null atau tidak memiliki format yang benar,
            // coba ambil dari record model
            $record = $this->getRecord();
            if ($record) {
                $this->state([
                    'latitude' => $record->latitude,
                    'longitude' => $record->longitude
                ]);
            }
        });
    }

    public function height(int $pixels): static
    {
        $this->height = $pixels;
        return $this;
    }

    public function tileLayers(array $layers): static
    {
        $this->tileLayers = $layers;
        return $this;
    }

    public function getExtraAttributes(): array
    {
        // dd(config('leaflet.max_zoom_level'));
        return [
            'height' => $this->height,
            'maxZoomLevel' => config('leaflet.max_zoom_level'),
            'tileLayers' => $this->tileLayers
        ];
    }
}
