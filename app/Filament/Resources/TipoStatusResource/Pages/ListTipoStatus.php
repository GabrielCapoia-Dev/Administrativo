<?php

namespace App\Filament\Resources\TipoStatusResource\Pages;

use App\Filament\Resources\TipoStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTipoStatus extends ListRecords
{
    protected static string $resource = TipoStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}