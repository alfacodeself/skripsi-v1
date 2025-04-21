<?php

namespace App\Filament\Resources;

use App\Enums\CustomerDocumentStatus;
use App\Enums\CustomerStatus;
use App\Filament\Resources\PelangganResource\Pages;
use App\Models\{JenisDokumen, Pelanggan};
use Carbon\Carbon;
use Filament\Actions\Action as ActionsAction;
use Filament\Forms\Components\{FileUpload, Repeater, RichEditor, Section, Select, TextInput, Toggle, Wizard};
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\{Form, Get, Set};
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Actions\{BulkActionGroup, DeleteBulkAction, EditAction, ActionGroup, Action};
use Filament\Tables\Columns\{ImageColumn, TextColumn};
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class PelangganResource extends Resource
{
    protected static ?string $model = Pelanggan::class;

    protected static ?string $recordTitleAttribute = 'nama';
    public static function getGlobalSearchResultTitle(Model $record): string|Htmlable
    {
        return $record->nama;
    }
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Nama' => $record->nama,
            'Email' => $record->email
        ];
    }
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Pelanggan';
    protected static ?string $navigationGroup = 'Data Pengguna';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Step::make('Data Pelanggan')
                        ->description('Masukkan Data Pelanggan')
                        ->icon('heroicon-o-user-circle')
                        ->completedIcon('heroicon-o-check-circle')
                        ->schema([
                            Section::make('Identitas Pelanggan')
                                ->schema([
                                    FileUpload::make('foto')
                                        ->directory('pelanggan/foto')
                                        ->image()
                                        ->avatar()
                                        ->imageEditor()
                                        ->circleCropper(),
                                    TextInput::make('nama')
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(fn(Set $set, ?string $state) => $set('kode', Str::upper(Str::random(6)) . Carbon::now()->format('YmdHis')))
                                        ->required(),
                                    TextInput::make('kode')
                                        ->required()
                                        ->unique(ignoreRecord: true)
                                        ->placeholder('Kode dibuat otomatis!')
                                        ->readOnly()
                                        ->maxLength(255),
                                    Select::make('status')
                                        ->required()
                                        ->placeholder('Pilih Status')
                                        ->options(CustomerStatus::class),
                                ]),
                        ]),
                    Step::make('Dokumen Pelanggan')
                        ->description('Masukkan Dokumen Pelanggan')
                        ->icon('heroicon-o-clipboard-document-check')
                        ->completedIcon('heroicon-o-check-circle')
                        ->schema([
                            Repeater::make('Dokumen')
                                ->relationship('dokumenPelanggan')
                                ->schema([
                                    Select::make('id_dokumen')
                                        ->label('Jenis Dokumen')
                                        ->reactive()
                                        ->relationship('jenisDokumen', 'nama_dokumen', fn($query) => $query->where('status', 'aktif'))
                                        ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                        ->required()
                                        ->afterStateUpdated(fn(callable $set, $state) => $set('is_dokumen_selected', $state !== null))
                                        ->afterStateHydrated(fn(callable $set, $state) => $set('is_dokumen_selected', $state !== null)),

                                    FileUpload::make('path')
                                        ->label('Upload Dokumen')
                                        ->directory(function (Get $get) {
                                            // Ambil id_dokumen yang dipilih
                                            $idDokumen = $get('id_dokumen');
                                            // Dapatkan path_dokumen dari database berdasarkan id_dokumen
                                            if ($idDokumen) {
                                                $jenisDokumen = JenisDokumen::find($idDokumen);
                                                return $jenisDokumen?->path_dokumen ?? 'pelanggan/dokumen';
                                            }

                                            // Default directory jika tidak ada id_dokumen
                                            return 'pelanggan/dokumen';
                                        })
                                        ->required()
                                        ->hidden(fn(callable $get) => !$get('is_dokumen_selected')),

                                    Select::make('status')
                                        ->required()
                                        ->options(CustomerDocumentStatus::class)
                                        ->hidden(fn(callable $get) => !$get('is_dokumen_selected')),

                                    RichEditor::make('catatan')
                                        ->hidden(fn(callable $get) => !$get('is_dokumen_selected')),

                                    Toggle::make('akses_pelanggan')
                                        ->hidden(fn(callable $get) => !$get('is_dokumen_selected')),
                                ])
                                ->addActionLabel('Tambah Dokumen Pelanggan')
                            // ->deletable(fn($operation): bool => $operation === 'create')
                        ]),
                    Step::make('Kredensial Pelanggan')
                        ->description('Masukkan Akun dan Kredensial Pelanggan')
                        ->icon('heroicon-o-key')
                        ->completedIcon('heroicon-o-check-circle')
                        ->schema([
                            Section::make('Kredensial Pelanggan')
                                ->schema([
                                    TextInput::make('email')
                                        ->required()
                                        ->unique(ignoreRecord: true)
                                        ->email()
                                        ->autocomplete(false),
                                    TextInput::make('telepon')
                                        ->required()
                                        ->unique(ignoreRecord: true)
                                        ->tel()
                                        ->numeric()
                                        ->mask('+62 999-9999-9999') // Menggunakan mask untuk format input
                                        ->rule('starts_with:+62') // Menambahkan aturan validasi khusus
                                        ->afterStateUpdated(fn(callable $set, $state) => $set('telepon', str_replace([' ', '-'], '', $state))), // Membersihkan input dari spasi dan strip
                                    TextInput::make('password')
                                        ->required(fn(string $operation): bool => $operation === 'create')
                                        ->password()
                                        ->revealable()
                                        ->minLength(8)
                                        ->confirmed(),
                                    TextInput::make('password_confirmation')
                                        ->label('Konfirmasi Password')
                                        ->requiredWith('password')
                                        ->password()
                                        ->minLength(8),
                                ]),
                        ])
                        ->visibleOn('create')
                ])
                    ->nextAction(fn($action) => $action->label('Lanjut'))
                    ->previousAction(fn($action) => $action->label('Kembali'))
                    ->skippable()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('foto')
                    ->circular(),
                TextColumn::make('nama')
                    ->searchable(),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('telepon')
                    ->searchable(),
                TextColumn::make('status')->badge(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make()->label('Ubah Pelanggan'),
                    Action::make('Status Pelanggan')
                        ->icon('heroicon-m-user-circle')
                        ->form([
                            Select::make('status')
                                ->required()
                                ->placeholder('Pilih Status')
                                ->options(CustomerStatus::class),
                            RichEditor::make('catatan')->placeholder('Tulis catatan anda kepada pelanggan jika diperlukan...')
                        ])
                        ->action(function (array $data, $record) {
                            $record->updateOrFail($data);
                            Notification::make()
                                ->title('Berhasil!')
                                ->success()
                                ->body('Berhasil mengubah status pelanggan.')
                                ->send();
                        }),
                    Action::make('Reset Password')
                        ->icon('heroicon-m-lock-closed')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function ($record) {
                            $newPassword = Str::random(10);
                            $record->update(['password' => bcrypt($newPassword)]);
                            Notification::make()
                                ->title('Berhasil! ' . $newPassword)
                                ->success()
                                ->body('Password baru pelanggan berhasil dikirim melalui email dan telepon.')
                                ->send();
                        }),
                ])
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
            'index' => Pages\ListPelanggans::route('/'),
            'create' => Pages\CreatePelanggan::route('/create'),
            'edit' => Pages\EditPelanggan::route('/{record}/edit'),
        ];
    }
}
