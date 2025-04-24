<?php

namespace App\Livewire\Table;

use App\Models\Tagihan;
use App\Models\TransaksiTagihan;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class TransactionList extends Component implements HasTable, HasForms
{
    use InteractsWithForms, InteractsWithTable;

    public ?Tagihan $tagihan = null;

    public function mount(Tagihan $tagihan)
    {
        $this->tagihan = $tagihan->load('transaksiTagihan');
        // dd($this->tagihan);
    }

    public function render()
    {
        return view('livewire.table.transaction-list');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                TransaksiTagihan::query()->where('id_tagihan', $this->tagihan->id)
            )
            ->heading('Histori Transaksi')
            ->columns([
                TextColumn::make('kode')->label('Kode Transaksi'),
                TextColumn::make('jumlah_bayar')
                    ->money('IDR', locale: 'id'),
                TextColumn::make('metode_pembayaran')->badge(),
                TextColumn::make('bukti_pembayaran')
                    ->label('Bukti Pembayaran')
                    ->formatStateUsing(fn($state) => $state ? 'Lihat Bukti' : '-')
                    ->url(fn($record) => $record->bukti_pembayaran ? Storage::url($record->bukti_pembayaran) : null, true)
                    ->openUrlInNewTab()
                    ->color(fn($record) => $record->bukti_pembayaran ? Color::Emerald : Color::Gray),
                TextColumn::make('tanggal_kadaluarsa')->date('Y F d'),
                TextColumn::make('tanggal_lunas')->date('Y F d'),
                TextColumn::make('status')->badge()
            ]);
    }
}
