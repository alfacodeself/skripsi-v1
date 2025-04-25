<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LandingpageResource\Pages;
use App\Filament\Resources\LandingpageResource\RelationManagers;
use App\Models\Landingpage;
use Filament\Forms;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LandingpageResource extends Resource
{
    protected static ?string $model = Landingpage::class;

    protected static ?string $navigationGroup = 'Tampilan';

    protected static ?string $navigationIcon = 'heroicon-o-window';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('navigasi')
                    ->label('Nama Navigasi')
                    ->required()
                    ->maxLength(255),

                TextInput::make('icon_navigasi')
                    ->label('Icon (misal: heroicon-m-home)')
                    ->required()
                    ->maxLength(255),

                TextInput::make('kode_navigasi')
                    ->label('Kode Navigasi')
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->maxLength(255),

                RichEditor::make('content')
                    ->label('Kode Konten')
                    ->placeholder('Masukkan HTML, Blade, atau JSON di sini...')
                    ->columnSpanFull()
                    ->required(),

                TextInput::make('order')
                    ->label('Urutan')
                    ->numeric()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('navigasi')
                    ->label('Navigasi')
                    ->searchable()
                    ->sortable(),

                IconColumn::make('icon_navigasi')
                    ->label('Icon')
                    ->icon(fn(string $state) => $state),

                TextColumn::make('kode_navigasi')
                    ->label('Kode')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('order')
                    ->label('Urutan')
                    ->sortable(),
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
            'index' => Pages\ListLandingpages::route('/'),
            'create' => Pages\CreateLandingpage::route('/create'),
            'edit' => Pages\EditLandingpage::route('/{record}/edit'),
        ];
    }
}
