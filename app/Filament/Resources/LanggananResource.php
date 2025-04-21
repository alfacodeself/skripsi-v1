<?php

namespace App\Filament\Resources;

use App\Enums\{SubscriptionProgressStatus, SubscriptionStatus};
use App\Filament\Resources\LanggananResource\Pages;
use App\Forms\Components\LeafletMap;
use App\Models\{Langganan, Paket, Pelanggan};
use Carbon\Carbon;
use Filament\Forms\Components\{DatePicker, FileUpload, Group, Hidden, Placeholder, Repeater, RichEditor, Section, Select, Textarea, Toggle};
use Filament\Forms\{Form, Get, Set};
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Actions\{Action, ActionGroup};
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\{Builder, Model};
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\{HtmlString, Str};
use Illuminate\Support\Facades\Blade;

class LanggananResource extends Resource
{
    protected static ?string $model = Langganan::class;

    protected static ?string $navigationIcon = 'heroicon-o-circle-stack';
    protected static ?string $recordTitleAttribute = 'kode_layanan';
    protected static ?int $navigationSort = 3;
    public static function getGlobalSearchResultTitle(Model $record): string|Htmlable
    {
        return $record->kode_layanan;
    }
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Paket' => $record->paket->nama . ' | (' . $record->paket->kategori->nama . ')',
            'Pelanggan' => $record->pelanggan->nama,
            'Status' => Str::upper($record->status->getLabel())
        ];
    }
    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['paket.kategori', 'pelanggan']);
    }
    protected static ?string $navigationLabel = 'Langganan';
    protected static ?string $navigationGroup = 'Data Layanan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                    ->schema([
                        Section::make('Data Pelanggan')
                            ->schema([
                                Select::make('id_pelanggan')
                                    ->label('Pelanggan')
                                    ->relationship(
                                        name: 'pelanggan',
                                        titleAttribute: 'nama',
                                        modifyQueryUsing: fn($query) => $query->where('status', 'aktif')
                                    )
                                    ->reactive()
                                    ->afterStateUpdated(function (Set $set, $state) {
                                        $pelanggan = Pelanggan::with(['dokumenPelanggan.jenisDokumen'])->find($state);
                                        // dd($pelanggan->toArray());
                                        $set('pelanggan', $pelanggan ? $pelanggan->toArray() : null);
                                    })
                                    ->afterStateHydrated(function (Set $set, $state) {
                                        $pelanggan = Pelanggan::with(['dokumenPelanggan.jenisDokumen'])->find($state);
                                        $set('pelanggan', $pelanggan ? $pelanggan->toArray() : null);
                                    })
                                    ->disabledOn('edit')
                                    ->searchable(['nama', 'email', 'telepon', 'kode'])
                                    ->searchPrompt('Cari pelanggan berdasarkan salah satu dari: kode, nama, email, dan telepon pelanggan')
                                    ->searchDebounce(500)
                                    ->required(),
                            ]),
                        Section::make('Data Paket')
                            ->schema([
                                Select::make('id_paket')
                                    ->label('Paket')
                                    ->relationship(
                                        name: 'paket',
                                        titleAttribute: 'nama',
                                        modifyQueryUsing: fn($query) => $query->where('status', 'aktif')->whereHas('kategori', fn($kategori) => $kategori->where('status', 'aktif'))
                                    )
                                    ->reactive()
                                    ->afterStateUpdated(function (Set $set, $state) {
                                        $paket = Paket::with('kategori')->find($state);
                                        $set('paket', $paket ? $paket->toArray() : null);
                                    })
                                    ->afterStateHydrated(function (Set $set, $state) {
                                        $paket = Paket::with('kategori')->find($state);
                                        $set('paket', $paket ? $paket->toArray() : null);
                                    })
                                    ->disabledOn('edit')
                                    ->searchable(['nama'])
                                    ->searchPrompt('Cari paket...')
                                    ->searchDebounce(500)
                                    ->preload()
                                    ->required(),
                            ]),
                        Section::make('Alamat Berlangganan')
                            ->schema([
                                LeafletMap::make('location')
                                    ->height(300)
                                    ->afterStateUpdated(function (Set $set, ?array $state) {
                                        $set('latitude', $state['latitude']);
                                        $set('longitude', $state['longitude']);
                                    })
                                    ->dehydrated()
                                    ->columnSpanFull(),
                                Hidden::make('latitude')
                                    ->default(config('leaflet.latitude')),
                                Hidden::make('longitude')
                                    ->default(config('leaflet.longitude')),
                                RichEditor::make('alamat_lengkap')
                                    ->label('Alamat Lengkap')
                                    ->placeholder('Alamat lengkap pelanggan...')
                                    ->required(),
                            ]),
                        Section::make('Jadwal / Tanggal')
                            ->schema([
                                DatePicker::make('tanggal_pemasangan')
                                    ->label('Tanggal Pemasangan')
                                    ->helperText('Tanggal perencanaan pemasangan instalasi internet.')
                                    ->required(),
                                DatePicker::make('tanggal_aktif')
                                    ->label('Tanggal Aktif')
                                    ->helperText('Tanggal langganan internet aktif.'),
                                DatePicker::make('tanggal_kadaluarsa')
                                    ->label('Tanggal Kadaluarsa')
                                    ->requiredWith('tanggal_aktif')
                                    ->helperText('Tanggal langganan internet kadaluarsa.')
                                    ->afterStateUpdated(function (Set $set, $state) {
                                        $set('createProgress', $state);
                                    }),
                            ]),
                        Section::make('Progress Langganan')
                            ->schema([
                                Repeater::make('Progress Langganan')
                                    ->relationship('progressLangganan')
                                    ->schema([
                                        Hidden::make('id'),
                                        Select::make('id_progress')
                                            ->label('Progress Langganan')
                                            ->reactive()
                                            ->relationship('progress', 'nama', fn($query) => $query->where('status', 'aktif')->orderBy('urutan', 'ASC'))
                                            ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                            ->required()
                                            ->afterStateUpdated(fn(callable $set, $state) => $set('is_progress_selected', $state !== null)),

                                        FileUpload::make('bukti')
                                            ->label('Upload Bukti Progress')
                                            ->directory('langganan/bukti')
                                            ->nullable()
                                            ->hidden(fn(callable $get) => !$get('is_progress_selected')),

                                        Select::make('status')
                                            ->required()
                                            ->options(SubscriptionProgressStatus::class)
                                            ->hidden(fn(callable $get) => !$get('is_progress_selected')),

                                        Textarea::make('keterangan')
                                            ->hidden(fn(callable $get) => !$get('is_progress_selected')),

                                        DatePicker::make('tanggal_perencanaan')
                                            ->nullable()
                                            ->label('Tanggal Perencanaan')
                                            ->helperText('Tanggal perencanaan pelaksanaan progress.')
                                            ->hidden(fn(callable $get) => !$get('is_progress_selected')),
                                    ])
                            ])
                            ->visibleOn('create'),
                        Section::make('Catatan Langganan')
                            ->schema([
                                RichEditor::make('catatan')
                                    ->hiddenLabel(),
                            ])
                    ])
                    ->columnSpan(2),

                Group::make()
                    ->schema([
                        Section::make('Informasi Pelanggan')
                            ->schema([
                                Placeholder::make('foto')
                                    ->label('foto')
                                    ->content(function (Get $get) {
                                        $foto = $get('pelanggan.foto') == null ? asset('assets/img/default-avatar.jpg') : Storage::url($get('pelanggan.foto'));
                                        return new HtmlString("<img src=$foto alt='foto-pelanggan' class='rounded-full' style='width: 200px'>");
                                    })
                                    ->extraAttributes([
                                        'class' => 'flex justify-center',
                                    ])
                                    ->hiddenLabel()
                                    ->columnSpanFull(),
                                Placeholder::make('nama')
                                    ->content(function (Get $get) {
                                        return $get('pelanggan.nama');
                                    }),
                                Placeholder::make('kode pelanggan')
                                    ->content(function (Get $get) {
                                        return $get('pelanggan.kode');
                                    }),
                                Placeholder::make('kontak')
                                    ->content(function (Get $get) {
                                        $email = $get('pelanggan.email');
                                        $telepon = $get('pelanggan.telepon');
                                        return $email . ' | ' . $telepon;
                                    }),
                                Placeholder::make('catatan')
                                    ->content(function (Get $get) {
                                        return new HtmlString($get('pelanggan.catatan') ?? 'Tidak ada catatan');
                                    }),
                                Placeholder::make('dokumen pelanggan')
                                    ->content(function (Get $get) {
                                        $dokumenPelanggan = $get('pelanggan.dokumen_pelanggan');
                                        $html = "<ol>";
                                        foreach ($dokumenPelanggan as $dokumen) {
                                            $namaDokumen = $dokumen['jenis_dokumen']['nama_dokumen'];
                                            $linkDokumen = Storage::url($dokumen['path']);
                                            $statusDokumen = Str::upper($dokumen['status']);
                                            $html .= "<li><a target='__blank' class='underline' href=$linkDokumen>$namaDokumen</a> ($statusDokumen)</li>";
                                        }
                                        $html .= "</ol>";
                                        return new HtmlString($html);
                                    }),
                            ])
                            ->visible(fn(Get $get): bool => $get('pelanggan') != null),
                        Section::make('Informasi Paket')
                            ->schema([
                                Placeholder::make('paket')
                                    ->content(function (Get $get) {
                                        $html = view('components.table.badge-with-text', [
                                            'badgeColor' => $get('paket.kategori')['warna'],
                                            'badgeName' => $get('paket.kategori')['nama'],
                                            'name' => $get('paket.nama')
                                        ])->render();
                                        return new HtmlString($html);
                                    }),
                                Placeholder::make('Deskripsi')->content(fn(Get $get) => new HtmlString($get('paket.deskripsi') ?: 'Tidak ada deskripsi')),
                                Placeholder::make('Harga')->content(fn(Get $get) => 'Rp. ' . number_format($get('paket.harga'))),
                                Placeholder::make('Layanan')->content(function (Get $get) {
                                    $html = '<table>';
                                    foreach ($get('paket.layanan') as $layanan) {
                                        $html .= '<tr>';
                                        $html .= '<td>';
                                        $html .= $layanan['jenis_layanan'];
                                        $html .= '</td>';
                                        $html .= '<td>';
                                        $html .= $layanan['status'] ? 'Tersedia' : 'Tidak Tersedia';
                                        $html .= '</td>';
                                        $html .= '</tr>';
                                    }
                                    $html .= '</table>';
                                    return new HtmlString($html);
                                }),
                            ])
                            ->visible(fn(Get $get): bool => $get('paket') != null)
                    ])
                    ->columnSpan(1),
            ])
            ->columns(3);
    }

    public static function getTableQuery(): Builder
    {
        return static::getModel()::query()->with(['pelanggan', 'paket.kategori', 'progressLangganan', 'tagihan']);
    }

    public static function table(Table $table): Table

    {
        return $table
            ->heading('Data Langganan')
            ->columns([
                TextColumn::make('paket')
                    ->label('Paket')
                    ->formatStateUsing(function ($state) {
                        return view('components.table.badge-with-text', [
                            'badgeColor' => $state->kategori->warna,
                            'badgeName' => $state->kategori->nama,
                            'name' => $state->nama
                        ])->render();
                    })
                    ->url(fn($state) => PaketResource::getUrl('view', [$state->slug]))
                    ->html(),
                TextColumn::make('pelanggan')
                    ->formatStateUsing(function ($state) {
                        return $state->nama;
                    })
                    ->url(fn($state) => PelangganResource::getUrl('edit', [$state->kode]))
                    ->sortable(),
                TextColumn::make('status')
                    ->sortable(),
                TextColumn::make('tanggal_pemasangan')
                    ->label('Pemasangan')
                    ->formatStateUsing(fn($state) => $state->translatedFormat('d F Y'))
                    ->sortable(),
                TextColumn::make('tanggal_aktif')
                    ->label('Tanggal Aktif')
                    ->formatStateUsing(fn($record) => '<center>' . $record->tanggal_aktif->translatedFormat('d F Y') . '<br>s/d<br>' . $record->tanggal_kadaluarsa->translatedFormat('d F Y') . '</center>')
                    ->html(),
            ])
            ->filters([
                SelectFilter::make('status')->options(SubscriptionStatus::class)
            ])
            ->searchable()
            ->searchDebounce('200')
            ->persistFiltersInSession()
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make()->label('Lihat Langganan'),
                    Action::make('Status Langganan')
                        ->icon('heroicon-m-adjustments-horizontal')
                        ->modal()
                        ->modalWidth(MaxWidth::Medium)
                        ->form([
                            Select::make('status')
                                ->required()
                                ->placeholder('Pilih Status')
                                ->options(SubscriptionStatus::AKTIF),
                            RichEditor::make('catatan')->placeholder('Tulis catatan anda untuk langganan jika diperlukan...')
                        ])
                        ->action(function (array $data, Langganan $record) {
                            if ($data['status'] === SubscriptionStatus::AKTIF->value) {
                                if (empty($record->tanggal_aktif) && empty($record->tanggal_kadaluarsa)) {
                                    $data['tanggal_aktif'] = Carbon::now();
                                    $data['tanggal_kadaluarsa'] = Carbon::now()->addDays($record->paket->kategori->jumlah_hari);
                                }
                            }
                            $record->updateOrFail($data);
                            Notification::make()
                                ->title('Berhasil!')
                                ->success()
                                ->body('Berhasil mengubah status langganan.')
                                ->send();
                        }),
                    Action::make('kelolaProgres')
                        ->label('Histori Progress')
                        ->icon('heroicon-o-list-bullet')
                        ->modalHeading(fn($record) => "Histori Progress Langganan : {$record->kode_layanan}")
                        ->modalWidth(MaxWidth::FiveExtraLarge)
                        ->modalContent(fn(Langganan $record) => new HtmlString(
                            Blade::render(
                                '<div>
                                    <livewire:table.progress-history-list :langganan="$langganan" lazy />
                                </div>',
                                ['langganan' => $record]
                            )
                        ))
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Tutup'),
                    Action::make('invoicesList')
                        ->label('List Tagihan')
                        ->icon('heroicon-o-clipboard-document-check')
                        ->modal()
                        ->modalHeading(fn($record) => "Detail Tagihan Langganan : {$record->kode_layanan}")
                        ->modalWidth(MaxWidth::SixExtraLarge)
                        ->modalContent(fn(Langganan $record) => new HtmlString(
                            Blade::render(
                                '<div>
                                    <livewire:table.invoice-list :langganan="$langganan" lazy/>
                                </div>',
                                ['langganan' => $record]
                            )
                        ))
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Tutup')

                ])
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
            'index' => Pages\ListLangganans::route('/'),
            'create' => Pages\CreateLangganan::route('/create'),
            'edit' => Pages\EditLangganan::route('/{record}/edit'),
        ];
    }
}
