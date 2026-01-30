<?php

namespace App\Filament\Resources\EscolaResource\Pages;

use App\Filament\Resources\EscolaResource;
use Filament\Actions;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewEscola extends ViewRecord
{
    protected static string $resource = EscolaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Dados da Escola')
                    ->icon('heroicon-o-building-library')
                    ->schema([
                        Infolists\Components\TextEntry::make('nome')
                            ->label('Nome'),

                        Infolists\Components\TextEntry::make('telefone')
                            ->label('Telefone')
                            ->placeholder('Não informado'),

                        Infolists\Components\TextEntry::make('email')
                            ->label('E-mail')
                            ->placeholder('Não informado'),

                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Cadastrado em')
                            ->dateTime('d/m/Y H:i'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Estatísticas')
                    ->icon('heroicon-o-chart-bar')
                    ->schema([
                        Infolists\Components\TextEntry::make('usuarios_count')
                            ->label('Total de Usuários')
                            ->state(fn($record) => $record->usuarios()->count())
                            ->badge()
                            ->color('info'),

                        Infolists\Components\TextEntry::make('pedidos_count')
                            ->label('Total de Pedidos')
                            ->state(fn($record) => $record->pedidos()->count())
                            ->badge()
                            ->color('warning'),

                        Infolists\Components\TextEntry::make('pedidos_abertos')
                            ->label('Pedidos em Aberto')
                            ->state(fn($record) => $record->pedidos()
                                ->whereHas('tipoStatus', fn($q) => $q->where('finaliza_pedido', false)->where('cancela_pedido', false))
                                ->count())
                            ->badge()
                            ->color('danger'),
                    ])
                    ->columns(3),
            ]);
    }
}