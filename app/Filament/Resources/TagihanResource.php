<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TagihanResource\Pages;
use App\Filament\Resources\TagihanResource\RelationManagers;
use App\Models\Tagihan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class TagihanResource extends Resource
{
    protected static ?string $model = Tagihan::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Tagihan';
    protected static ?string $navigationGroup = 'Data Keuangan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('total_tagihan')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('sisa_tagihan')
                    ->required()
                    ->numeric(),
                Forms\Components\DatePicker::make('jatuh_tempo')
                    ->required(),
                Forms\Components\TextInput::make('status_angsuran')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('jumlah_angsuran')
                    ->numeric(),
                Forms\Components\TextInput::make('status')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('langganan.kode_layanan')
                    ->formatStateUsing(function ($state, $record) {
                        return view('components.table.invoice-detail', [
                            'url' => LanggananResource::getUrl('edit', [$state]),
                            'langgananId' => $state,
                            'totalTagihan' => $record->total_tagihan,
                            'sisaTagihan' => $record->sisa_tagihan,
                            'jatuhTempo' => $record->jatuh_tempo,
                        ]);
                    })
                    ->label('Tagihan'),
                Tables\Columns\TextColumn::make('status_angsuran')
                    ->formatStateUsing(fn ($state, $record) => view('components.table.installment-detail', [
                        'statusAngsuran' => $state,
                        'jumlahAngsuran' => $record->jumlah_angsuran,
                    ])),
                Tables\Columns\TextColumn::make('status')->badge(),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make()
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
            'index' => Pages\ListTagihans::route('/'),
            'create' => Pages\CreateTagihan::route('/create'),
            'edit' => Pages\EditTagihan::route('/{record}/edit'),
        ];
    }
}
