<?php

namespace App\Filament\Resources;

use App\Enums\DefaultStatus;
use App\Filament\Exports\KategoriExporter;
use App\Filament\Imports\KategoriImporter;
use App\Filament\Resources\KategoriResource\Pages;
use App\Filament\Resources\KategoriResource\RelationManagers;
use App\Models\Kategori;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class KategoriResource extends Resource
{
    protected static ?string $model = Kategori::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
    protected static ?string $navigationLabel = 'Kategori';
    protected static ?string $navigationGroup = 'Data Layanan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Kategori')
                    ->schema([
                        Forms\Components\TextInput::make('nama')
                            ->required()
                            ->maxLength(255)
                            ->label('Nama Kategori')
                            ->placeholder('Cth: Kategori #1')
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn(Set $set, ?string $state) => $set('slug', Str::slug($state) . '-' . Carbon::now()->format('Y-m-d-His')))
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->placeholder('Slug dibuat otomatis!')
                            ->readOnly()
                            ->maxLength(255)
                            ->columnSpan(1),
                        Forms\Components\RichEditor::make('deskripsi')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\Select::make('status')
                            ->required()
                            ->options(DefaultStatus::class),
                        Forms\Components\TextInput::make('jumlah_hari')
                            ->required()
                            ->minLength(1)
                            ->maxLength(3)
                            ->numeric(),
                        Forms\Components\ColorPicker::make('warna')
                            ->columnSpanFull()
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->heading('Data Kategori')
            ->headerActions([
                Tables\Actions\ExportAction::make()
                    ->label('Export')
                    ->icon('heroicon-m-printer')
                    ->color('success')
                    ->exporter(KategoriExporter::class),
                Tables\Actions\ImportAction::make()
                    ->label('Import')
                    ->icon('heroicon-m-arrow-up-tray')
                    ->color('info')
                    ->importer(KategoriImporter::class)
            ])
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\ColorColumn::make('warna')
                    ->copyable()
                    ->copyMessage('Warna berhasil di salin!')
                    ->copyMessageDuration(1500)
                    ->searchable(),
                Tables\Columns\TextColumn::make('jumlah_hari')
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
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\Action::make('aktif')
                        ->icon('heroicon-m-power')
                        ->label('Aktifkan')
                        ->color('success')
                        ->action(function (Kategori $record) {
                            $record->update(['status' => 'aktif']);
                            Notification::make()
                                ->title('Berhasil!')
                                ->success()
                                ->body('Kategori : ' . $record->nama . ' berhasil diaktifkan.')
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->visible(fn(Kategori $record) => $record->status == 'tidak aktif'),
                    Tables\Actions\Action::make('nonaktif')
                        ->icon('heroicon-m-power')
                        ->label('Nonaktifkan')
                        ->color('danger')
                        ->action(function (Kategori $record) {
                            $record->update(['status' => 'tidak aktif']);
                            Notification::make()
                                ->title('Berhasil!')
                                ->success()
                                ->body('Kategori : ' . $record->nama . ' berhasil dinonaktifkan.')
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->visible(fn(Kategori $record) => $record->status == 'aktif'),
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
            'index' => Pages\ListKategoris::route('/'),
            'create' => Pages\CreateKategori::route('/create'),
            'edit' => Pages\EditKategori::route('/{record}/edit'),
        ];
    }
}
