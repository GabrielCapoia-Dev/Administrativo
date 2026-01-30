<?php

namespace App\Filament\Resources\TipoStatusResource\Pages;

use App\Filament\Resources\TipoStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTipoStatus extends EditRecord
{
    protected static string $resource = TipoStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}