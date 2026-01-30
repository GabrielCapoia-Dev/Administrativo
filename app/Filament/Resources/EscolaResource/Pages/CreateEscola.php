<?php

namespace App\Filament\Resources\EscolaResource\Pages;

use App\Filament\Resources\EscolaResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEscola extends CreateRecord
{
    protected static string $resource = EscolaResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}