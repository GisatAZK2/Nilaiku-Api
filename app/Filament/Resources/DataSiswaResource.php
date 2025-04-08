<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DataSiswaResource\Pages;
use App\Filament\Resources\DataSiswaResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;


class DataSiswaResource extends Resource
{
    protected static ?string $model = \App\Models\Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Data Siswa';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('Name')
        ->required()
        ->maxLength(255),
    TextInput::make('Email')
        ->required()
        ->maxLength(255),
        TextInput::make('Password')
        ->required()
        ->maxLength(255),
    Section::make('Gender')
        ->description('Jenis Kelamin')
        ->schema([
            Select::make('status')
                ->options([
                    'L' => 'Laki-laki',
                    'P' => 'Perempuan',
                ])
                ->required(),
           
        ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->query(function () {
             $userIds = cache()->remember('user-table-siswa-ids', now()->addMinutes(10), function () {
                return \App\Models\User::where('role', 'siswa')->pluck('id')->toArray();
            });

            return \App\Models\Student::whereIn('user_id', $userIds);
        })
                ->columns([
                    Tables\Columns\TextColumn::make('id')
                        ->sortable()
                        ->label('ID')
                        ->rowindex()
                        ->searchable(),
                    Tables\Columns\TextColumn::make('name')
                        ->sortable()
                        ->label('Nama')
                        ->searchable(),
                    Tables\Columns\TextColumn::make('gender')
                        ->sortable()
                        ->label('Jenis Kelamin')
                        ->searchable(),
                    Tables\Columns\TextColumn::make('date_of_birth')
                        ->sortable()
                        ->label('Tanggal Lahir')
                        ->searchable(),
                    Tables\Columns\TextColumn::make('user.email')
                        ->sortable()
                        ->label('Email')
                        ->searchable(),
                    Tables\Columns\TextColumn::make('created_at')
                        ->dateTime()
                        ->sortable()
                        ->searchable(),  
            ])
            ->filters([
                
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
            'index' => Pages\ListDataSiswas::route('/'),
            'edit' => Pages\EditDataSiswa::route('/{record}/edit'),
        ];
    }
}
