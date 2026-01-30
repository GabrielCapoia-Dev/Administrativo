<?php

namespace App\Filament\Resources\TipoStatusResource\Pages;

use App\Filament\Resources\TipoStatusResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTipoStatus extends CreateRecord
{
    protected static string $resource = TipoStatusResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}