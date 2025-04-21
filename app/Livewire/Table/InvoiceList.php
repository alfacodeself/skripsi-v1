<?php

namespace App\Livewire\Table;

use App\Enums\InvoiceStatus;
use App\Enums\PaymentMethod;
use App\Enums\TransactionStatus;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use App\Models\{DetailTagihan, Langganan, Tagihan, TransaksiTagihan};
use Carbon\Carbon;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Livewire\Attributes\On;
use Livewire\Component;
use Midtrans\Config;
use Midtrans\Snap;

class InvoiceList extends Component implements HasTable, HasForms
{
    use InteractsWithTable, InteractsWithForms;

    public ?Langganan $langganan = null;

    public function mount(Langganan $langganan): void
    {
        $this->langganan = $langganan->load(['paket.biayaPaket.jenisBiaya', 'pelanggan']);
    }

    public function render()
    {
        return view('livewire.table.invoice-list');
    }

    #[On('handleMidtrans')]
    public function handleMidtransResponse(bool $closed, array $response = [])
    {
        $orderId = $response['order_id'] ?? null;
        $status = $response['transaction_status'] ?? 'unknown';
        $paidAmount = (int) ($response['gross_amount'] ?? 0);

        $transaction = TransaksiTagihan::with('tagihan.langganan.paket')
            ->where('kode', $orderId)
            ->where('status', TransactionStatus::WAITING->value)
            ->firstOrFail();
        $tagihan = $transaction->tagihan;

        if ($closed && $orderId) {
            $transaction->updateOrFail(['status' => TransactionStatus::CANCELLED]);
            Notification::make()
                ->title('Transaksi Dibatalkan')
                ->body('Kamu menutup atau membatalkan pembayaran.')
                ->danger()
                ->send();
            return;
        }

        $notification = Notification::make()
            ->title('Status: ' . ucfirst($response['transaction_status'] ?? 'Unknown Status'))
            ->body('Order ID: ' . ($response['order_id'] ?? '-'));

        switch ($status) {
            case 'settlement':
            case 'capture':
                $transaction->updateOrFail(['status' => TransactionStatus::PAID]);
                $currentInvoiceAmount = (int) $tagihan->sisa_tagihan - $paidAmount;
                if ($currentInvoiceAmount <= 0) {
                    $tagihan->updateOrFail([
                        'status' => InvoiceStatus::LUNAS,
                        'sisa_tagihan' => 0
                    ]);
                } elseif ($tagihan->status_angsuran) {
                    $durasiHariAngsuran = $tagihan->langganan->paket->durasi_hari_angsuran;
                    $oneDayBeforeExpireDate = Carbon::parse($tagihan->langganan->tanggal_kadaluarsa)->subDay();

                    $tagihan->updateOrFail([
                        'status' => InvoiceStatus::DALAM_ANGSURAN,
                        'sisa_tagihan' => $currentInvoiceAmount,
                        'jatuh_tempo' => Carbon::now()->addDays($durasiHariAngsuran)->greaterThan($oneDayBeforeExpireDate) ? $oneDayBeforeExpireDate : Carbon::now()->addDays($durasiHariAngsuran),
                    ]);
                }

                $notification->success()->send();
                break;
            case 'pending':
                $transaction->updateOrFail(['status' => TransactionStatus::PENDING]);
                $notification->warning()->send();
                break;
            case 'deny':
            case 'failure':
                $transaction->updateOrFail(['status' => TransactionStatus::FAILED]);
                $notification->danger()->send();
                break;
            case 'cancel':
            case 'expire':
                $transaction->updateOrFail(['status' => TransactionStatus::EXPIRED]);
                $notification->danger()->send();
                break;
            default:
                $notification->warning()->send();
                break;
        }

        $this->resetTable();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Tagihan::query()
                    ->with(['detailTagihan.biayaPaket.jenisBiaya'])
                    ->where('id_langganan', $this->langganan->id)
            )
            ->heading('List Tagihan')
            ->columns([
                TextColumn::make('index')
                    ->label('Tagihan Ke-')
                    ->rowIndex(false),
                TextColumn::make('total_tagihan')
                    ->label('Total Tagihan')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('sisa_tagihan')
                    ->label('Sisa Tagihan')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('jatuh_tempo')
                    ->label('Jatuh Tempo')
                    ->date('d F Y')
                    ->sortable(),
                TextColumn::make('status_angsuran')
                    ->label('Berangsur (Y/N)')
                    ->formatStateUsing(fn($state) => $state ? 'Y' : 'N'),
                TextColumn::make('jumlah_angsuran')
                    ->label('Angsuran (Rp.)')
                    ->money('IDR'),
                TextColumn::make('status')
                    ->badge()
            ])
            ->headerActions([
                Action::make('generateInvoice')
                    ->label('Buat Tagihan Baru')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->color(Color::Emerald)
                    ->modal()
                    ->modalWidth(MaxWidth::Small)
                    ->form(function () {
                        return [
                            Placeholder::make('biaya_paket')
                                ->label('Rincian Biaya')
                                ->content(function () {
                                    $langganan = $this->langganan;
                                    $biayaPaket = $langganan->paket->biayaPaket;
                                    $totalBiaya = 0;

                                    $table = '<div class="w-full overflow-x-auto rounded">
                                    <table class="w-full bg-white border border-gray-200 shadow-sm rounded-lg">
                                        <thead class="bg-gray-100">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-b">
                                                    Jenis Biaya
                                                </th>
                                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider border-b">
                                                    Harga
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100 text-sm text-gray-700">';

                                    foreach ($biayaPaket as $biaya) {
                                        $jumlahBiaya = $biaya->jenisBiaya->jenis_biaya === 'flat'
                                            ? $biaya->besar_biaya
                                            : ($biaya->besar_biaya / 100 * $totalBiaya);

                                        $totalBiaya += $jumlahBiaya;

                                        $table .= '<tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-4 py-2">' . $biaya->jenisBiaya->nama . '</td>
                                            <td class="px-4 py-2 text-right">Rp ' . number_format($jumlahBiaya, 0, ',', '.') . '</td>
                                        </tr>';
                                    }

                                    $table .= '<tr class="bg-emerald-50 font-semibold border-t border-gray-200">
                                        <td class="px-4 py-3 text-emerald-700">Total Biaya</td>
                                        <td class="px-4 py-3 text-right text-emerald-700">Rp ' . number_format($totalBiaya, 0, ',', '.') . '</td>
                                    </tr>';
                                    $table .= '</tbody></table></div>';
                                    return new HtmlString($table);
                                })
                                ->columnSpanFull(),
                            Toggle::make('angsur')
                                ->label('Bayar Dengan Angsuran')
                                ->inline(false)
                                ->visible(fn() => $this->langganan->paket->bisa_diangsur)
                        ];
                    })
                    ->action(function (array $data) {
                        $langganan = $this->langganan;
                        $biayaPaket = $langganan->paket->biayaPaket;
                        $totalTagihan = 0;

                        $tagihan = Tagihan::create([
                            'id_langganan' => $langganan->id,
                            'total_tagihan' => $totalTagihan,
                            'sisa_tagihan' => $totalTagihan,
                            'jatuh_tempo' => now()->addDays(1),
                            'status_angsuran' => count($data) != 0 && $data['angsur'] ? true : false,
                            'jumlah_angsuran' => count($data) != 0 && $data['angsur'] ? $langganan->paket->minimal_jumlah_angsuran : null,
                            'status' => 'belum lunas',
                        ]);

                        foreach ($biayaPaket as $biaya) {
                            $jenisBiaya = $biaya->jenisBiaya;
                            $jumlahBiaya = $jenisBiaya->jenis_biaya === 'flat'
                                ? $biaya->besar_biaya
                                : ($biaya->besar_biaya / 100 * $totalTagihan);

                            DetailTagihan::create([
                                'id_tagihan' => $tagihan->id,
                                'id_biaya_paket' => $biaya->id,
                                'keterangan' => $jenisBiaya->nama,
                                'jumlah_biaya' => $jumlahBiaya,
                            ]);

                            $totalTagihan += $jumlahBiaya;
                        }

                        $tagihan->update([
                            'total_tagihan' => $totalTagihan,
                            'sisa_tagihan' => $totalTagihan
                        ]);

                        Notification::make()
                            ->title('Berhasil!')
                            ->success()
                            ->body('Tagihan langganan berhasil dibuat.')
                            ->send();
                    })
                    ->visible(fn() => $this->langganan->tagihan->whereIn('status', [InvoiceStatus::LUNAS, InvoiceStatus::DALAM_ANGSURAN])->count() == 0),
            ])
            ->actions([
                Action::make('payment')
                    ->label('Bayar Tagihan')
                    ->icon('heroicon-o-banknotes')
                    ->color(Color::Indigo)
                    ->modal()
                    ->modalHeading('Metode Pembayaran')
                    ->modalWidth(MaxWidth::ExtraSmall)
                    ->modalContent(fn() => new HtmlString('Silakan pilih metode pembayaran di bawah ini:'))
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false)
                    ->modalActions([
                        Action::make('paymentGateway')
                            ->label('Payment Gateway')
                            ->color(Color::Amber)
                            ->action(function (Tagihan $record, Action $action) {
                                // Config Midtrans
                                Config::$serverKey = config('midtrans.serverKey');
                                Config::$isProduction = config('midtrans.isProduction');
                                Config::$isSanitized = config('midtrans.isSanitized');
                                Config::$is3ds = config('midtrans.is3ds');

                                $transactionDetails = $this->getTransactionDetails($record);
                                $customerDetails = $this->getCustomerDetails();
                                $itemDetails = $this->getItemDetails($record);

                                $snapToken = Snap::getSnapToken([
                                    'transaction_details' => $transactionDetails,
                                    'customer_details' => $customerDetails,
                                    'item_details' => $itemDetails
                                ]);

                                $this->midtransConfigJS($snapToken, $transactionDetails['order_id']);

                                $this->generateTransactionToDatabase($record, $transactionDetails['order_id'], $transactionDetails['gross_amount'], PaymentMethod::MIDTRANS);
                            }),
                        Action::make('cash')
                            ->label('Bayar Manual (Cash)')
                            ->button()
                            ->color('danger')
                            ->action(function (Tagihan $record) {
                                Notification::make()
                                    ->title('Silakan unggah bukti pembayaran')
                                    ->body('Pembayaran dicatat sebagai manual.')
                                    ->success()
                                    ->send();
                            }),
                    ])
                    ->visible(fn($record) => $record->status == InvoiceStatus::BELUM_LUNAS || $record->status == InvoiceStatus::DALAM_ANGSURAN),
                ActionGroup::make([
                    Action::make('invoiceDetail')
                        ->label('Detail Tagihan')
                        ->icon('heroicon-o-eye')
                        ->modalHeading('Detail Tagihan')
                        ->modalWidth(MaxWidth::Large)
                        ->modalContent(fn(Tagihan $record) => new HtmlString(
                            Blade::render(
                                '<div>
                                    <livewire:table.invoice-detail-list :tagihan="$tagihan" />
                                </div>',
                                ['tagihan' => $record]
                            )
                        ))
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Tutup')
                ])
            ]);
    }

    // Helper function
    protected function generateTransactionID(): string
    {
        return 'TRX-' . $this->langganan->kode_layanan . '-' . Carbon::now()->format('Y-m-d-His');
    }

    protected function getItemDetails(Tagihan $record): array
    {
        if ($record->status_angsuran && $record->jumlah_angsuran != 0) {
            $angsuranKe = $record->transaksiTagihan()->where('status', 'lunas')->count() + 1;
            return [[
                'id' => 'ANGSURAN-' . $record->id . '-' . $angsuranKe,
                'price' => min($record->sisa_tagihan, $record->jumlah_angsuran),
                'quantity' => 1,
                'name' => "Angsuran ke-{$angsuranKe} dari {$record->jumlah_angsuran} - {$record->langganan->paket->nama}"
            ]];
        }
        return $record->detailTagihan->map(function ($detail, $i) {
            return [
                'id' => 'ITEM-' . $detail->id,
                'price' => (int) $detail->jumlah_biaya,
                'quantity' => 1,
                'name' => $detail->keterangan ?? 'Biaya Layanan #' . $i
            ];
        })
            ->toArray();
    }

    protected function getTransactionDetails(Tagihan $record): array
    {
        return [
            'order_id' => $this->generateTransactionID(),
            'gross_amount' => $this->getGrandTotal($record)
        ];
    }

    protected function getCustomerDetails(): array
    {
        return [
            'first_name' => $this->langganan->pelanggan->nama,
            'email' => $this->langganan->pelanggan->email,
            'phone' => $this->langganan->pelanggan->telepon
        ];
    }

    protected function getGrandTotal(Tagihan $record): int|float
    {
        // Kalau status tagihannya angsuran dan sisa tagihannya lebih besar dari jumlah angsuran maka bayarnya sejumlah angsuran,
        // Kalau sisa tagihannya lebih sedikit dari angsuran maka yang diutamakan adalah sisa tagihannya
        $sisa = (int) $record->sisa_tagihan;
        $angsuran = (int) $record->jumlah_angsuran;

        return !$record->status_angsuran
            ? $sisa
            : min($sisa, $angsuran);
    }

    protected function generateTransactionToDatabase(Tagihan $record, string $transactionID, int $totalAmount, PaymentMethod $paymentMethod): void
    {
        $record->transaksiTagihan()->create([
            'kode' => $transactionID,
            'jumlah_bayar' => $totalAmount,
            'metode_pembayaran' => $paymentMethod->value,
        ]);
    }

    protected function midtransConfigJS(string $snapToken, string $transactionID): void
    {
        $this->js(<<<JS
            (() => {
                const token = "{$snapToken}";
                const trxID = "{$transactionID}";

                function loadSnap(callback) {
                    if (window.snap) {
                        return callback();
                    }

                    // Cek apakah script snap sudah dimuat sebelumnya
                    if (document.getElementById('midtrans-snap-script')) {
                        waitForSnap(callback);
                        return;
                    }

                    const script = document.createElement('script');
                    script.src = "https://app.sandbox.midtrans.com/snap/snap.js";
                    script.setAttribute('data-client-key', "{{ config('midtrans.clientKey') }}");
                    script.id = 'midtrans-snap-script';

                    script.onload = () => {
                        console.log('✅ Midtrans Snap loaded');
                        callback();
                    };

                    script.onerror = () => {
                        alert("Gagal memuat Snap.js dari Midtrans");
                    };

                    document.head.appendChild(script);
                }

                function waitForSnap(callback, retries = 10) {
                    if (typeof window.snap === 'undefined') {
                        if (retries <= 0) {
                            alert("Gagal memuat Midtrans Snap. Coba refresh halaman.");
                            return;
                        }
                        setTimeout(() => waitForSnap(callback, retries - 1), 300);
                    } else {
                        callback();
                    }
                }

                const sendToLivewire = (response, closed = false) => {
                    Livewire.dispatch('handleMidtrans', { closed, response });
                };

                loadSnap(() => {
                    window.snap.pay(token, {
                        onSuccess: function(result) {
                            sendToLivewire(result, false);
                            console.log("✅ Pembayaran berhasil");
                        },
                        onPending: function(result) {
                            sendToLivewire(result, false);
                            console.log("⏳ Menunggu pembayaran");
                        },
                        onError: function(result) {
                            sendToLivewire(result, false);
                            console.error("❌ Terjadi kesalahan pembayaran");
                        },
                        onClose: function() {
                            sendToLivewire({ transaction_status: 'closed_by_user', order_id: trxID }, true);
                            console.log("❎ Popup ditutup tanpa membayar");
                        }
                    });
                    if (window.snap.setFinishCallback) {
                        window.snap.setFinishCallback(function(result) {
                            sendToLivewire(result, false);
                        });
                    }
                });
            })();
        JS);
    }
}
