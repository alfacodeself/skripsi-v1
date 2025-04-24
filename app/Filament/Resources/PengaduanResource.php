<?php

namespace App\Filament\Resources;

use App\Enums\ReportStatus;
use App\Filament\Resources\PengaduanResource\Pages;
use App\Filament\Resources\PengaduanResource\RelationManagers;
use App\Models\Pengaduan;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PengaduanResource extends Resource
{
    protected static ?string $model = Pengaduan::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-exclamation';
    protected static ?string $navigationLabel = 'Pengaduan';
    protected static ?string $navigationGroup = 'Bantuan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('id_langganan')
                    ->label('Pilih Langganan')
                    ->relationship(
                        name: 'langganan',
                        titleAttribute: 'kode_layanan', // tetap diperlukan tapi akan dioverride
                        modifyQueryUsing: fn($query) => $query->where('status', 'aktif')
                    )
                    ->getOptionLabelFromRecordUsing(
                        fn($record) =>
                        "{$record->kode_layanan} - {$record->paket->nama} - {$record->pelanggan->nama}"
                    )
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('id_kategori_pengaduan')
                    ->label('Pilih Kategori Pengaduan')
                    ->relationship(
                        name: 'kategoriPengaduan',
                        titleAttribute: 'kategori', // tetap diperlukan tapi akan dioverride
                        modifyQueryUsing: fn($query) => $query->where('status', 'aktif')
                    )
                    ->searchable()
                    ->preload()
                    ->required(),
                RichEditor::make('deskripsi')->required()->columnSpanFull(),
                FileUpload::make('lampiran')
                    ->label('Upload Lampiran')
                    ->directory('pengaduan/lampiran')
                    ->required()
                    ->columnSpanFull()
            ]);
    }

    public static function getTableQuery(): Builder
    {
        return static::getModel()::query()->with(['langganan', 'kategoriPengaduan', 'admin']);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('langganan.kode_layanan')
                    ->label('Kode Langganan')
                    ->url(fn($state) => LanggananResource::getUrl('edit', [$state]), true),
                TextColumn::make('kategoriPengaduan.kategori')
                    ->label('Kategori'),
                TextColumn::make('lampiran')
                    ->formatStateUsing(fn() => 'Lihat Lampiran')
                    ->url(fn($state) => Storage::url($state), true),
                TextColumn::make('status')->badge(),
                ImageColumn::make('admin.foto')
                    ->label('Ditangani Oleh')
                    ->circular()
                    ->getStateUsing(fn($record) => $record->admin?->foto)
                    ->url(
                        fn($record) => $record->admin
                            ? AdminResource::getUrl('edit', [$record->admin->id])
                            : null
                    ),
                TextColumn::make('tanggal_selesai')
                    ->dateTime('Y F d')
                    ->label('Tanggal Selesai')
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->label('Ubah Pengaduan'),
                    Action::make('Tanggapi')
                        ->label('Tanggapi')
                        ->icon('heroicon-o-chat-bubble-left-ellipsis')
                        ->visible(
                            fn($record) =>
                            $record->ditangani_oleh === null ||
                                $record->ditangani_oleh === Auth::id() ||
                                Auth::guard('admin')->user()->superadmin
                        )
                        ->form([
                            Forms\Components\Select::make('status')
                                ->options(ReportStatus::class)
                                ->required()
                                ->default(fn($record) => $record->status),

                            Forms\Components\RichEditor::make('tanggapan')
                                ->label('Tanggapan')
                                ->required()
                                ->default(fn($record) => $record->tanggapan),
                        ])
                        ->action(function ($record, array $data) {
                            $record->status = $data['status'];
                            $record->tanggapan = $data['tanggapan'];
                            $record->ditangani_oleh = Auth::guard('admin')->id();

                            if ($data['status'] === ReportStatus::FINISHED) {
                                $record->tanggal_selesai = Carbon::now();
                            }

                            $record->save();
                        })
                        ->modalHeading('Tanggapi Pengaduan')
                        ->modalSubmitActionLabel('Simpan'),

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
            'index' => Pages\ListPengaduans::route('/'),
            'create' => Pages\CreatePengaduan::route('/create'),
            'edit' => Pages\EditPengaduan::route('/{record}/edit'),
        ];
    }
}
