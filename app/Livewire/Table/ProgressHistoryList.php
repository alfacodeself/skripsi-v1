<?php

namespace App\Livewire\Table;

use App\Enums\SubscriptionProgressStatus;
use App\Models\{ProgressLangganan, Langganan};
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Support\Colors\Color;
use Filament\Tables\Actions\{Action, ActionGroup};
use Filament\Tables\Columns\{TextColumn};
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class ProgressHistoryList extends Component implements HasTable, HasForms
{
    use InteractsWithTable, InteractsWithForms;

    public ?Langganan $langganan = null;

    public function mount(Langganan $langganan): void
    {
        $this->langganan = $langganan->load('paket.biayaPaket.jenisBiaya');
    }

    public function render()
    {
        return view('livewire.table.progress-history-list');
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Histori Progress Langganan')
            ->query(
                ProgressLangganan::query()
                    ->with(['progress', 'langganan'])
                    ->where('id_langganan', $this->langganan->id)
            )
            ->columns([
                TextColumn::make('progress')
                    ->formatStateUsing(function ($state, $record) {
                        $nama = $state->nama;
                        $keterangan = $record->keterangan;
                        return "<b>{$nama}</b><br>{$keterangan}";
                    })
                    ->html(),
                TextColumn::make('bukti')
                    ->label('Bukti Progress')
                    ->formatStateUsing(fn($record) => $record->bukti ? '<span class="text-primary-500 font-medium">Lihat Bukti</span>' : '-')
                    ->url(fn($record) => $record->bukti ? Storage::url($record->bukti) : null, true)
                    ->html()
                    ->openUrlInNewTab(),
                TextColumn::make('status')->badge(),
                TextColumn::make('tanggal_perencanaan')
                    ->label('Perencanaan')
                    ->formatStateUsing(fn($state) => $state->translatedFormat('d F Y')),
            ])
            ->headerActions([
                Action::make('tambahProgress')
                    ->label('Tambah Progress Baru')
                    ->icon('heroicon-o-plus')
                    ->color(Color::Emerald)
                    ->form(function () {
                        $langganan = $this->langganan->load('progressLangganan');
                        return [
                            Select::make('id_progress')
                                ->label('Progress')
                                ->relationship(
                                    'progress',
                                    'nama',
                                    function ($query) use ($langganan) {
                                        // Ambil id progress yang sudah ada di progress_langganan
                                        $existingProgressIds = $langganan->progressLangganan->pluck('id_progress');
                                        // Filter out progress yang sudah ada
                                        return $query->whereNotIn('id', $existingProgressIds)
                                            ->orderBy('urutan', 'asc');
                                    }
                                )
                                ->required(),

                            Select::make('status')
                                ->required()
                                ->options(SubscriptionProgressStatus::class),

                            FileUpload::make('bukti')
                                ->label('Upload Bukti Progress')
                                ->directory('langganan/bukti')
                                ->nullable(),
                            Textarea::make('keterangan'),

                            DatePicker::make('tanggal_perencanaan')
                                ->nullable()
                                ->label('Tanggal Perencanaan'),
                        ];
                    })
                    ->action(function ($data) {
                        ProgressLangganan::create(['id_langganan' => $this->langganan->id, ...$data]);
                        Notification::make()
                            ->title('Berhasil! ')
                            ->success()
                            ->body('Berhasil menambahkan progress langganan baru.')
                            ->send();
                    })
                    ->modalSubmitActionLabel('Tambah Progress')
                    ->modalCancelActionLabel('Batal')
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('editProgress')
                        ->label('Edit Progress')
                        ->icon('heroicon-o-pencil-square')
                        ->form(function ($record) {
                            return [
                                Select::make('status')
                                    ->required()
                                    ->options(SubscriptionProgressStatus::class)
                                    ->default($record->status),

                                FileUpload::make('bukti')
                                    ->label('Upload Bukti Progress')
                                    ->directory('langganan/bukti')
                                    ->nullable()
                                    ->default($record->bukti)
                                    ->downloadable()
                                    ->openable(),

                                Textarea::make('keterangan')
                                    ->default($record->keterangan),

                                DatePicker::make('tanggal_perencanaan')
                                    ->nullable()
                                    ->default($record->tanggal_perencanaan)
                                    ->label('Tanggal Perencanaan'),
                            ];
                        })
                        ->action(function ($record, $data) {
                            $record->update($data);
                            Notification::make()
                                ->title('Berhasil!')
                                ->success()
                                ->body('Progress langganan berhasil diperbarui.')
                                ->send();
                        })
                        ->modalSubmitActionLabel('Edit Progress')
                        ->modalCancelActionLabel('Batal'),
                    Action::make('deleteProgress')
                        ->label('Hapus Progress')
                        ->color('danger')
                        ->icon('heroicon-o-trash')
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Progress')
                        ->modalDescription('Apakah anda yakin ingin menghapus progress ini? Data yang dihapus tidak dapat dikembalikan.')
                        ->modalSubmitActionLabel('Ya, Hapus')
                        ->modalCancelActionLabel('Batal')
                        ->action(function ($record) {
                            $record->delete();
                            Notification::make()
                                ->title('Berhasil!')
                                ->success()
                                ->body('Progress langganan berhasil dihapus.')
                                ->send();
                        }),
                ])
            ]);
    }
}
