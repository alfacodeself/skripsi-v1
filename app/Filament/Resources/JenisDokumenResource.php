<?php

namespace App\Filament\Resources;

use App\Enums\DefaultStatus;
use App\Filament\Resources\JenisDokumenResource\Pages;
use App\Filament\Resources\JenisDokumenResource\RelationManagers;
use App\Models\JenisDokumen;
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

class JenisDokumenResource extends Resource
{
    protected static ?string $model = JenisDokumen::class;

    protected static ?string $recordTitleAttribute = 'nama_dokumen';
    public static function getGlobalSearchResultTitle(Model $record): string|Htmlable
    {
        return $record->nama_dokumen;
    }
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Jenis Dokumen' => $record->nama_dokumen,
            'Status' => Str::upper($record->status->getLabel())
        ];
    }
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Jenis Dokumen';
    protected static ?string $navigationGroup = 'Data Manajemen';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Data Jenis Dokumen')
                    ->schema([
                        Forms\Components\TextInput::make('nama_dokumen')
                            ->label('Nama Dokumen')
                            ->required()
                            ->maxLength(150),
                        Forms\Components\TextInput::make('path_dokumen')
                            ->label('Path Dokumen')
                            ->helperText('Base path folder dokumen di storage')
                            ->required()
                            ->maxLength(150),
                        Forms\Components\FileUpload::make('contoh_dokumen')
                            ->label('Contoh Dokumen')
                            ->directory('dokumen/contoh-dokumen/')
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\RichEditor::make('deskripsi_dokumen')
                            ->label('Deskripsi Dokumen')
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\Select::make('status')
                            ->options(DefaultStatus::class)
                            ->required(),
                    ])
                    ->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_dokumen')
                    ->label('Nama Dokumen')
                    ->searchable(),
                Tables\Columns\TextColumn::make('path_dokumen')
                    ->label('Path Dokumen')
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
                                ->body('Jenis Dokumen : ' . $record->nama_dokumen . ' berhasil diaktifkan.')
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
                                ->body('Jenis Dokumen : ' . $record->nama_dokumen . ' berhasil dinonaktifkan.')
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
            'index' => Pages\ListJenisDokumens::route('/'),
            'create' => Pages\CreateJenisDokumen::route('/create'),
            'edit' => Pages\EditJenisDokumen::route('/{record}/edit'),
        ];
    }
}
