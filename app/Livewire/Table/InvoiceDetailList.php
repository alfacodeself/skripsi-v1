<?php

namespace App\Livewire\Table;

use App\Models\DetailTagihan;
use App\Models\Tagihan;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;

class InvoiceDetailList extends Component implements HasTable, HasForms
{
    use InteractsWithTable, InteractsWithForms;

    public ?Tagihan $tagihan = null;

    public function mount(Tagihan $tagihan): void
    {
        $this->tagihan = $tagihan;
    }

    public function render()
    {
        return view('livewire.table.invoice-detail-list');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                DetailTagihan::query()
                    ->where('id_tagihan', $this->tagihan->id)
            )
            ->heading('List Detail Tagihan')
            ->columns([
                TextColumn::make('index')
                    ->label('No.')
                    ->rowIndex(false),
                TextColumn::make('keterangan')
                    ->label('Jenis Pembayaran'),
                TextColumn::make('jumlah_biaya')
                    ->label('Harga')
                    ->money('IDR')
                    ->sortable(),
            ]);
    }
}
