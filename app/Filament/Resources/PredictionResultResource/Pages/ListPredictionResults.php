<?php

namespace App\Filament\Resources\PredictionResultResource\Pages;

use App\Filament\Resources\PredictionResultResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPredictionResults extends ListRecords
{
    protected static string $resource = PredictionResultResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
