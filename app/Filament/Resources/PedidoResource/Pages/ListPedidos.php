<?php

namespace App\Filament\Resources\PedidoResource\Pages;

use App\Filament\Resources\PedidoResource;
use App\Services\PedidoService;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use App\Models\TipoStatus;

class ListPedidos extends ListRecords
{
    protected static string $resource = PedidoResource::class;

    protected function getHeaderActions(): array
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user?->hasPermissionTo('Criar Pedidos')) {
            return [
                Actions\CreateAction::make()
                    ->label('Novo Pedido'),
            ];
        }

        return [];
    }
    public function getTabs(): array
    {
        $service = app(PedidoService::class);

        // Só servidor educação vê as tabs
        if (!$service->ehServidorEducacao(Auth::user())) {
            return [];
        }

        $tabs = [
            'todos' => Tab::make('Todos'),
        ];

        // Cria uma tab para cada status
        $statusList = TipoStatus::where('ativo', true)->orderBy('ordem')->get();

        foreach ($statusList as $status) {
            $tabs[$status->id] = Tab::make($status->nome)
                ->modifyQueryUsing(fn(Builder $query) => $query->where('tipo_status_id', $status->id))
                ->badge(
                    fn() => \App\Models\Pedido::where('ativo', true)
                        ->where('tipo_status_id', $status->id)
                        ->count()
                );
        }

        return $tabs;
    }
}
