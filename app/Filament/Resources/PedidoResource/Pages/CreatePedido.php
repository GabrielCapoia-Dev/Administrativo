<?php

namespace App\Filament\Resources\PedidoResource\Pages;

use App\Filament\Resources\PedidoResource;
use App\Models\PedidoFoto;
use App\Services\PedidoService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreatePedido extends CreateRecord
{
    protected static string $resource = PedidoResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Remove fotos do array principal (serão salvas separadamente)
        unset($data['fotos']);
        
        return $data;
    }

    protected function afterCreate(): void
    {
        $fotos = $this->data['fotos'] ?? [];
        
        foreach ($fotos as $caminho) {
            PedidoFoto::create([
                'pedido_id' => $this->record->id,
                'caminho' => $caminho,
                'nome_original' => basename($caminho),
            ]);
        }
    }

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $service = app(PedidoService::class);
        return $service->criarPedido($data, Auth::user());
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Pedido criado com sucesso! Protocolo: ' . $this->record->numero_protocolo;
    }
}