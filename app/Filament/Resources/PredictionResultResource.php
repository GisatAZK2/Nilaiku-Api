<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PredictionResultResource\Pages;
use App\Filament\Resources\PredictionResultResource\RelationManagers;
use App\Models\PredictionResult;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PredictionResultResource extends Resource
{
    protected static ?string $model = PredictionResult::class;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-bar';
    protected static ?string $navigationGroup = 'Prediction';

    public static function canCreate(): bool
    {
       return false;
    }
   

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                ->sortable()
                ->label('ID')
                ->rowindex()
                ->searchable(),
            Tables\Columns\TextColumn::make('AcademicRecord.student.name')
                ->sortable()
                ->label('Nama Siswa')
                ->searchable(),
            Tables\Columns\TextColumn::make('AcademicRecord.subject.name')
                ->sortable()
                ->label('Mata Pelajaran')
                ->searchable(),
            Tables\Columns\TextColumn::make('predicted_score')
                ->sortable()
                ->label('Hasil Prediksi'),
                Tables\Columns\TextColumn::make('recommendation')
                ->sortable()
                ->label('Di Rekomendasikan'),
           
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
            'index' => Pages\ListPredictionResults::route('/'),
            'create' => Pages\CreatePredictionResult::route('/create'),
            'edit' => Pages\EditPredictionResult::route('/{record}/edit'),
        ];
    }
}
