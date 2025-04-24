<?php

namespace App\Filament\Resources;

use App\Enums\DefaultStatus;
use App\Filament\Resources\AreaResource\Pages;
use App\Filament\Resources\AreaResource\RelationManagers;
use App\Forms\Components\LeafletMap;
use App\Models\Area;
use Filament\Forms;
use Filament\Forms\Components\{Hidden, RichEditor, Select, TextInput};
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class AreaResource extends Resource
{
    protected static ?string $model = Area::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';
    protected static ?string $navigationLabel = 'Area Tercakup';
    protected static ?string $navigationGroup = 'Data Manajemen';

    protected static ?string $recordTitleAttribute = 'alamat_lengkap';
    public static function getGlobalSearchResultTitle(Model $record): string|Htmlable
    {
        return $record->alamat_lengkap;
    }
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Alamat' => $record->alamat_lengkap,
            'Kecamatan' => $record->kecamatan,
            'Kabupaten' => $record->kabupaten,
            'Provinsi' => $record->provinsi,
            'Kode Pos' => $record->kode_pos,
            'Status' => Str::upper($record->status->getLabel())
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                LeafletMap::make('location')
                    ->height(300)
                    ->afterStateUpdated(function (Set $set, ?array $state) {
                        // dd($state);
                        $set('latitude', $state['latitude']);
                        $set('longitude', $state['longitude']);
                    })
                    ->dehydrated()
                    ->columnSpanFull(),
                Hidden::make('latitude')
                    ->default(config('leaflet.latitude')),
                Hidden::make('longitude')
                    ->default(config('leaflet.longitude')),
                // Provinsi
                TextInput::make('provinsi')
                    ->label('Provinsi')
                    ->required()
                    ->maxLength(255),

                // Kabupaten
                TextInput::make('kabupaten')
                    ->label('Kabupaten')
                    ->required()
                    ->maxLength(255),

                // Kecamatan
                TextInput::make('kecamatan')
                    ->label('Kecamatan')
                    ->required()
                    ->maxLength(255),

                // Kode Pos
                TextInput::make('kode_pos')
                    ->label('Kode Pos')
                    ->required()
                    ->maxLength(10)
                    ->numeric(),

                // Alamat Lengkap (Rich Text Editor)
                RichEditor::make('alamat_lengkap')
                    ->label('Alamat Lengkap')
                    ->required()
                    ->maxLength(5000)
                    ->columnSpanFull(),

                // Status (Select)
                Select::make('status')
                    ->label('Status')
                    ->options(DefaultStatus::class)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('alamat_lengkap')
                    ->label('Alamat Lengkap')
                    ->sortable()
                    ->html()
                    ->searchable(),

                // Kolom untuk menampilkan latitude
                TextColumn::make('latitude')
                    ->label('Latitude')
                    ->sortable()
                    ->searchable(),

                // Kolom untuk menampilkan longitude
                TextColumn::make('longitude')
                    ->label('Longitude')
                    ->sortable()
                    ->searchable(),

                // Kolom untuk memeriksa apakah lokasi aktif (misalnya)
                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),

                // Opsional: Kolom dengan link untuk melihat di peta
                TextColumn::make('created_at')
                    ->label('View on Map')
                    ->formatStateUsing(fn() => 'Lihat di Map')
                    ->url(fn($record) => 'https://www.openstreetmap.org/?mlat=' . $record->latitude . '&mlon=' . $record->longitude . '#map=12/' . $record->latitude . '/' . $record->longitude, true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAreas::route('/'),
            'create' => Pages\CreateArea::route('/create'),
            'edit' => Pages\EditArea::route('/{record}/edit'),
        ];
    }

    // Fungsi untuk mendapatkan alamat berdasarkan koordinat
    protected function getAddressFromCoordinates($latitude, $longitude)
    {
        // Di sini, kamu bisa menggunakan API geocoding seperti Nominatim atau Google Maps API
        // Misalnya menggunakan API Nominatim (OpenStreetMap)

        $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$latitude}&lon={$longitude}&addressdetails=1";
        $response = file_get_contents($url);
        $data = json_decode($response, true);

        if ($data && isset($data['address'])) {
            // Mengambil alamat dari response API
            return $data['address']['road'] ?? 'Unknown Address';
        }

        return 'Unknown Address';
    }
}
