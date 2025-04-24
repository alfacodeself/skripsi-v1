<?php

namespace App\Filament\Resources;

use App\Enums\DefaultStatus;
use App\Filament\Resources\KategoriPengaduanResource\Pages;
use App\Filament\Resources\KategoriPengaduanResource\RelationManagers;
use App\Models\KategoriPengaduan;
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

class KategoriPengaduanResource extends Resource
{
    protected static ?string $model = KategoriPengaduan::class;

    protected static ?string $navigationIcon = 'heroicon-o-bug-ant';

    protected static ?string $navigationLabel = 'Kategori Pengaduan';
    protected static ?string $navigationGroup = 'Data Manajemen';

    protected static ?string $recordTitleAttribute = 'kategori';
    public static function getGlobalSearchResultTitle(Model $record): string|Htmlable
    {
        return $record->kategori;
    }
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Kategori Pengaduan' => $record->kategori,
            'Status' => Str::upper($record->status->getLabel())
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('kategori')
                    ->label('Kategori Pengaduan')
                    ->required()
                    ->placeholder('Kategori Pengaduan')
                    ->maxLength(255)
                    ->helperText('Cth: Internet Lamban, Internet Mati'),
                Forms\Components\Select::make('status')
                    ->options(DefaultStatus::class)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kategori')
                    ->label('Kategori Pengaduan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
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
            'index' => Pages\ListKategoriPengaduans::route('/'),
            'create' => Pages\CreateKategoriPengaduan::route('/create'),
            'edit' => Pages\EditKategoriPengaduan::route('/{record}/edit'),
        ];
    }
}
