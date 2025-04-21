<?php

namespace App\Filament\Resources;

use App\Enums\DefaultStatus;
use App\Filament\Resources\PaketResource\Pages;
use App\Filament\Resources\PaketResource\RelationManagers;
use App\Models\{Paket, JenisBiaya};
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Support\Htmlable;

class PaketResource extends Resource
{
    protected static string | array $routeMiddleware = ['admin.kategori.null', 'admin.jenis-biaya.null'];

    protected static ?string $model = Paket::class;

    protected static ?string $recordTitleAttribute = 'nama';
    public static function getGlobalSearchResultTitle(Model $record): string|Htmlable
    {
        return $record->nama;
    }
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Paket' => $record->nama,
            'Kategori' => $record->kategori->nama,
            'Harga' => 'Rp. ' . number_format($record->harga),
        ];
    }
    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with('kategori');
    }

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Paket';
    protected static ?string $navigationGroup = 'Data Layanan';

    public static function getTableQuery(): Builder
    {
        return static::getModel()::query()->with(['kategori', 'biayaPaket.jenis_biaya']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Paket')
                    ->schema([
                        Forms\Components\TextInput::make('nama')
                            ->required()
                            ->maxLength(255)
                            ->label('Nama Paket')
                            ->placeholder('Cth: Paket #1')
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn(Set $set, ?string $state) => $set('slug', Str::slug($state) . '-' . Carbon::now()->format('Y-m-d-His')))
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->placeholder('Slug dibuat otomatis!')
                            ->readOnly()
                            ->maxLength(255)
                            ->columnSpan(1),
                        Forms\Components\Select::make('id_kategori')
                            ->label('Kategori Paket')
                            ->searchable()
                            ->preload()
                            ->relationship('kategori', 'nama', fn(Builder $query) => $query->where('status', 'aktif'))
                            ->required()
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('harga')
                            ->prefix('IDR')
                            ->prefixIcon('heroicon-o-currency-dollar')
                            ->required()
                            ->numeric(),
                        Forms\Components\RichEditor::make('deskripsi')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\Repeater::make('layanan')
                            ->schema([
                                Forms\Components\TextInput::make('jenis_layanan')
                                    ->placeholder('Layanan yang ditawarkan paket ini')
                                    ->required(),
                                Forms\Components\Toggle::make('status')
                                    ->onColor('success')
                                    ->inline(false)
                            ])
                            ->columnSpanFull()
                            ->columns(2),
                        Forms\Components\Select::make('status')
                            ->required()
                            ->options(DefaultStatus::class)
                            ->columns(1),
                        Forms\Components\Toggle::make('bisa_diangsur')
                            ->label('Bisa Diangsur')
                            ->helperText('Aktifkan jika paket bisa diangsur')
                            ->onColor('success')
                            ->inline(false)
                            ->live()
                            ->columns(1),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Ketentuan Angsuran')
                    ->schema([
                        Forms\Components\TextInput::make('maksimal_angsuran')
                            ->numeric()
                            ->label('Maksimal Angsuran')
                            ->required()
                            ->suffix('Kali')
                            ->helperText('Maksimal nominal angsuran'),
                        Forms\Components\TextInput::make('minimal_jumlah_angsuran')
                            ->numeric()
                            ->label('Minimal Pembayaran per Angsuran')
                            ->prefix('IDR')
                            ->required()
                            ->prefixIcon('heroicon-o-banknotes')
                            ->helperText('Minimal jumlah pembayaran angsuran'),
                        Forms\Components\TextInput::make('durasi_hari_angsuran')
                            ->numeric()
                            ->label('Durasi Hari Angsuran')
                            ->suffix('Hari')
                            ->required()
                            ->helperText('Durasi Jarak Untuk Angsuran Berikutnya'),
                    ])
                    ->columns(3)
                    ->hidden(fn(Get $get): bool => !$get('bisa_diangsur')),

                Forms\Components\Section::make('Pengaturan Biaya Paket')
                    ->schema([
                        Forms\Components\Repeater::make('biaya_paket')
                            ->relationship('biayaPaket') // Relasi ke model BiayaPaket
                            ->schema([
                                Forms\Components\Select::make('id_jenis_biaya')
                                    ->label('Jenis Biaya')
                                    ->reactive()
                                    ->relationship('jenisBiaya', 'nama', fn(Builder $query) => $query->where('status', 'aktif'))
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                    ->required(),
                                Forms\Components\TextInput::make('besar_biaya')
                                    ->label('Nominal')
                                    ->placeholder('200000')
                                    ->numeric()
                                    ->prefix('IDR')
                                    ->required(),
                                Forms\Components\TextInput::make('keterangan')
                                    ->label('Keterangan Biaya')
                                    ->placeholder('Biaya Paket 35 Mbps')
                                    ->required(),
                                Forms\Components\Select::make('status')
                                    ->required()
                                    ->options(DefaultStatus::class),
                            ])
                            ->columns(4)
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->heading('Data Paket')
            ->columns([
                Tables\Columns\TextColumn::make('kategori')
                    ->label('Paket')
                    ->formatStateUsing(function ($state, $record) {
                        return view('components.table.badge-with-text', [
                            'badgeColor' => $state->warna,
                            'badgeName' => $state->nama,
                            'name' => $record->nama
                        ])->render();
                    })
                    ->html(),
                Tables\Columns\TextColumn::make('harga')
                    ->money('IDR', locale: 'id')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->since()
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Terakhir Diperbarui')
                    ->since()
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Tables\Filters\Filter::make('bisa_diangsur')->toggle()->query(fn($query) => $query->where('bisa_diangsur', true)),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\Action::make('aktif')
                        ->icon('heroicon-m-power')
                        ->label('Aktifkan')
                        ->color('success')
                        ->action(function ($record) {
                            $record->update(['status' => 'aktif']);
                            Notification::make()
                                ->title('Berhasil!')
                                ->success()
                                ->body('Paket : ' . $record->nama . ' berhasil diaktifkan.')
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->visible(fn($record) => $record->status == 'tidak aktif'),
                    Tables\Actions\Action::make('nonaktif')
                        ->icon('heroicon-m-power')
                        ->label('Nonaktifkan')
                        ->color('danger')
                        ->action(function ($record) {
                            $record->update(['status' => 'tidak aktif']);
                            Notification::make()
                                ->title('Berhasil!')
                                ->success()
                                ->body('Paket : ' . $record->nama . ' berhasil dinonaktifkan.')
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->visible(fn($record) => $record->status == 'aktif'),
                ]),
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
            'index' => Pages\ListPakets::route('/'),
            'create' => Pages\CreatePaket::route('/create'),
            'edit' => Pages\EditPaket::route('/{record}/edit'),
            'view' => Pages\ViewPaket::route('/{record}'),
        ];
    }
}
