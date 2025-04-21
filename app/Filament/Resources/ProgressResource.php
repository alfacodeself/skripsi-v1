<?php

namespace App\Filament\Resources;

use App\Enums\DefaultStatus;
use App\Filament\Resources\ProgressResource\Pages;
use App\Filament\Resources\ProgressResource\RelationManagers;
use App\Models\Progress;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class ProgressResource extends Resource
{
    protected static ?string $model = Progress::class;
    protected static ?string $recordTitleAttribute = 'nama';
    public static function getGlobalSearchResultTitle(Model $record): string|Htmlable
    {
        return $record->nama;
    }
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Nama Progress' => $record->nama,
            'Status' => Str::upper($record->status->getLabel())
        ];
    }
    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';
    protected static ?string $navigationLabel = 'Progress';
    protected static ?string $navigationGroup = 'Data Manajemen';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Data Progress')
                    ->schema([
                        Forms\Components\TextInput::make('nama')
                            ->required()
                            ->placeholder('Progress Berlangganan')
                            ->maxLength(255)
                            ->helperText('Cth: Pengajuan Langganan, Pemasangan Instalasi, dll'),
                        Forms\Components\TextInput::make('urutan')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->placeholder('Urutan Progress')
                            ->numeric()
                            ->helperText('Semakin kecil semakin didahulukan.'),
                        Forms\Components\Select::make('status')
                            ->options(DefaultStatus::class)
                            ->required(),
                    ])->columns(3)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->heading('Data Progress')
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('urutan')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
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
            'index' => Pages\ListProgress::route('/'),
            'create' => Pages\CreateProgress::route('/create'),
            'edit' => Pages\EditProgress::route('/{record}/edit'),
        ];
    }
}
