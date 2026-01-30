<?php

namespace App\Filament\Resources\PedidoResource\Pages;

use App\Filament\Resources\PedidoResource;
use App\Models\TipoStatus;
use App\Services\PedidoService;
use Filament\Actions;
use Filament\Forms;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class ViewPedido extends ViewRecord
{
    protected static string $resource = PedidoResource::class;

    protected function getHeaderActions(): array
    {
        $service = app(PedidoService::class);
        $actions = [];

        if ($service->ehServidorEducacao(Auth::user())) {
            $actions[] = Actions\EditAction::make();

            $actions[] = Actions\Action::make('alterarStatus')
                ->label('Alterar Status')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->form([
                    Forms\Components\Select::make('tipo_status_id')
                        ->label('Novo Status')
                        ->options(TipoStatus::where('ativo', true)->orderBy('ordem')->pluck('nome', 'id'))
                        ->required(),

                    Forms\Components\Textarea::make('observacao')
                        ->label('Observação')
                        ->rows(3),
                ])
                ->action(function (array $data) use ($service) {
                    $novoStatus = TipoStatus::find($data['tipo_status_id']);
                    $service->alterarStatus($this->record, $novoStatus, Auth::user(), $data['observacao'] ?? null);

                    Notification::make()
                        ->success()
                        ->title('Status alterado com sucesso')
                        ->send();

                    $this->refreshFormData(['tipo_status_id']);
                });
        }

        return $actions;
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informações do Pedido')
                    ->schema([
                        Infolists\Components\TextEntry::make('numero_protocolo')
                            ->label('Protocolo')
                            ->badge()
                            ->color('primary'),

                        Infolists\Components\TextEntry::make('tipoStatus.nome')
                            ->label('Status')
                            ->badge(),

                        Infolists\Components\TextEntry::make('tipoManutencao.nome')
                            ->label('Tipo de Manutenção')
                            ->badge()
                            ->color('gray'),

                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Criado em')
                            ->dateTime('d/m/Y H:i'),

                        Infolists\Components\TextEntry::make('data_prevista')
                            ->label('Previsão')
                            ->date('d/m/Y'),

                        Infolists\Components\TextEntry::make('data_entrega')
                            ->label('Entregue em')
                            ->date('d/m/Y')
                            ->placeholder('Não entregue'),
                    ])
                    ->columns(3),

                Infolists\Components\Section::make('Descrição')
                    ->schema([
                        Infolists\Components\TextEntry::make('descricao')
                            ->label('')
                            ->markdown()
                            ->columnSpanFull(),
                    ]),

                Infolists\Components\Section::make('Responsáveis')
                    ->schema([
                        Infolists\Components\TextEntry::make('escola.nome')
                            ->label('Escola'),

                        Infolists\Components\TextEntry::make('solicitante.name')
                            ->label('Solicitante'),

                        Infolists\Components\TextEntry::make('responsavelEducacao.name')
                            ->label('Responsável Educação')
                            ->placeholder('Não atribuído'),

                        Infolists\Components\TextEntry::make('responsavelObras.name')
                            ->label('Responsável Obras')
                            ->placeholder('Não atribuído'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Fotos')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('fotos')
                            ->label('')
                            ->schema([
                                Infolists\Components\ImageEntry::make('caminho')
                                    ->label('')
                                    ->disk('public')
                                    ->height(200),
                            ])
                            ->columns(3)
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ]);
    }
}