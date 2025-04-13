<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeedbackResource\Pages;
use App\Filament\Resources\FeedbackResource\RelationManagers;
use App\Models\Feedbacks;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FeedbackResource extends Resource
{
    protected static ?string $model = Feedbacks::class;

    protected static ?string $navigationIcon = 'heroicon-o-face-smile';

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
                    Tables\Columns\TextColumn::make('prediction_id')
                    ->sortable()
                    ->label('ID')
                    ->rowindex()
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->sortable()
                    ->label('Nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('comment')
                    ->sortable()
                    ->label('Prediksi Nilai'),
                Tables\Columns\TextColumn::make('rating')
                    ->sortable()
                    ->label('Rating'),
                Tables\Columns\TextColumn::make('date')
                    ->sortable()
                    ->dateTime()
                    ->label('Tanggal Dibuat'),
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
            'index' => Pages\ListFeedback::route('/'),
            //'create' => Pages\CreateFeedback::route('/create'),
            //'edit' => Pages\EditFeedback::route('/{record}/edit'),
        ];
    }
}
