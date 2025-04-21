<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TagihanResource\Pages;
use App\Filament\Resources\TagihanResource\RelationManagers;
use App\Models\Tagihan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class TagihanResource extends Resource
{
    protected static ?string $model = Tagihan::class;
    // protected static ?string $recordTitleAttribute = 'nama';
    // public static function getGlobalSearchResultTitle(Model $record): string|Htmlable
    // {
    //     return $record->nama;
    // }
    // public static function getGlobalSearchResultDetails(Model $record): array
    // {
    //     return [
    //         'Langganan' => $record->langganan,
    //         'Total Tagihan' => $record->total_tagihan,
    //         'Sisa Tagihan' => $record->sisa_tagihan,
    //         'Jatuh Tempo' => $record->jatuh_tempo,
    //         'Status' => Str::upper($record->status)
    //     ];
    // }
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Tagihan';
    protected static ?string $navigationGroup = 'Data Keuangan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id_langganan')
                    ->required()
                    ->numeric(),
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
                Tables\Columns\TextColumn::make('id_langganan')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_tagihan')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sisa_tagihan')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jatuh_tempo')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status_angsuran')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jumlah_angsuran')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListTagihans::route('/'),
            'create' => Pages\CreateTagihan::route('/create'),
            'edit' => Pages\EditTagihan::route('/{record}/edit'),
        ];
    }
}
