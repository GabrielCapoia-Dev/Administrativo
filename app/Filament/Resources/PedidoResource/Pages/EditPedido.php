<?php

namespace App\Filament\Resources\PedidoResource\Pages;

use App\Filament\Resources\PedidoResource;
use App\Models\TipoStatus;
use App\Services\PedidoService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditPedido extends EditRecord
{
    protected static string $resource = PedidoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Se mudou o status, registra no histórico
        $observacao = $data['observacao_status'] ?? null;
        unset($data['observacao_status']);

        return $data;
    }

    protected function afterSave(): void
    {
        $service = app(PedidoService::class);
        
        // Se o status mudou, registra no histórico
        if ($this->record->wasChanged('tipo_status_id')) {
            $novoStatus = TipoStatus::find($this->record->tipo_status_id);
            $observacao = $this->data['observacao_status'] ?? 'Status atualizado';
            
            $service->registrarHistorico(
                $this->record,
                $novoStatus,
                Auth::user(),
                $observacao
            );
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}