<?php

namespace App\Filament\Resources;

use App\Enums\DefaultStatus;
use App\Filament\Resources\JenisBiayaResource\Pages;
use App\Filament\Resources\JenisBiayaResource\RelationManagers;
use App\Models\JenisBiaya;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class JenisBiayaResource extends Resource
{
    protected static ?string $model = JenisBiaya::class;
    protected static ?string $recordTitleAttribute = 'nama';
    public static function getGlobalSearchResultTitle(Model $record): string|Htmlable
    {
        return $record->nama;
    }
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        $status = $record->berulang ? '(Pembayaran Berulang)' : '(Sekali Bayar)';
        return [
            'Nama Biaya' => $record->nama . ' ' . $status,
            'Jenis Biaya' => Str::upper($record->jenis_biaya),
            'Status' => Str::upper($record->status->getLabel())
        ];
    }
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Jenis Biaya';
    protected static ?string $navigationGroup = 'Data Manajemen';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Data Jenis Biaya')
                    ->schema([
                        Forms\Components\TextInput::make('nama')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('jenis_biaya')
                            ->options([
                                "persentase" => 'Persentase',
                                "flat" => 'Flat'
                            ])
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options(DefaultStatus::class)
                            ->required(),
                        Forms\Components\Toggle::make('berulang')
                            ->default(true)
                            ->inline(false)
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Biaya')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jenis_biaya')
                    ->label('Jenis Biaya')
                    ->getStateUsing(fn($record) => Str::upper($record->jenis_biaya)),
                Tables\Columns\TextColumn::make('berulang')
                    ->label('Jenis Pembayaran')
                    ->badge()
                    ->getStateUsing(fn($record) => $record->berulang ? 'Pembayaran Berulang' : 'Sekali Bayar')
                    ->searchable(),
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
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\Action::make('aktif')
                        ->icon('heroicon-m-power')
                        ->label('Aktifkan')
                        ->color('success')
                        ->action(function ($record) {
                            $record->update(['status' => 'aktif']);
                            Notification::make()
                                ->title('Berhasil!')
                                ->success()
                                ->body('Jenis Biaya : ' . $record->nama . ' berhasil diaktifkan.')
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->visible(fn($record) => $record->status == 'tidak aktif'),
                    Tables\Actions\Action::make('nonaktif')
                        ->icon('heroicon-m-power')
                        ->label('Nonaktifkan')
                        ->color('danger')
                        ->action(function ($record) {
                            $record->update(['status' => 'tidak aktif']);
                            Notification::make()
                                ->title('Berhasil!')
                                ->success()
                                ->body('Jenis Biaya : ' . $record->nama . ' berhasil dinonaktifkan.')
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->visible(fn($record) => $record->status == 'aktif'),
                ]),
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
            'index' => Pages\ListJenisBiayas::route('/'),
            'create' => Pages\CreateJenisBiaya::route('/create'),
            'edit' => Pages\EditJenisBiaya::route('/{record}/edit'),
        ];
    }
}
