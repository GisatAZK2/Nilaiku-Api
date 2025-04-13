<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReportsResource\Pages;
use App\Filament\Resources\ReportsResource\RelationManagers;
use App\Models\Reports;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReportsResource extends Resource
{
    protected static ?string $model = Reports::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-americas';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function canCreate(): bool
    {
       return false;
    }
   

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //ViewColumn::make('report_file')->view('filament.tables.columns.download-report'),

                Tables\Columns\TextColumn::make('id')
                        ->sortable()
                        ->label('ID')
                        ->rowindex()
                        ->searchable(),
                    Tables\Columns\TextColumn::make('user.name')
                        ->sortable()
                        ->label('Nama')
                        ->searchable(),
                    Tables\Columns\TextColumn::make('report_type')
                        ->sortable()
                        ->label('Tipe Laporan')
                        ->searchable(),
                    Tables\Columns\TextColumn::make('generate_date')
                        ->sortable()
                        ->label('Tanggal Dibuat')
                        ->date(),
                    Tables\Columns\ViewColumn::make('report_file')
                        ->sortable()
                        ->label('File Laporan')
                        ->view('filament.download-report')
                
            ])
            ->filters([
                //
            ])
            ->actions([
                //Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
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
            'index' => Pages\ListReports::route('/'),
            'create' => Pages\CreateReports::route('/create'),
            'edit' => Pages\EditReports::route('/{record}/edit'),
        ];
    }
}
