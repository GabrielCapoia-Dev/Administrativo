<?php

namespace App\Filament\Resources\EscolaResource\RelationManagers;

use App\Models\Pedido;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PedidosRelationManager extends RelationManager
{
    protected static string $relationship = 'pedidos';
    protected static ?string $title = 'Pedidos de Manutenção';
    protected static ?string $modelLabel = 'Pedido';
    protected static ?string $pluralModelLabel = 'Pedidos';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn() => $this->ownerRecord->pedidos()->where('ativo', true))
            ->columns([
                Tables\Columns\TextColumn::make('numero_protocolo')
                    ->label('Protocolo')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('tipoManutencao.nome')
                    ->label('Tipo')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('tipoStatus.nome')
                    ->label('Status')
                    ->badge(),

                Tables\Columns\TextColumn::make('solicitante.name')
                    ->label('Solicitante')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('data_prevista')
                    ->label('Previsão')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\Action::make('ver')
                    ->label('Ver')
                    ->icon('heroicon-o-eye')
                    ->url(fn(Pedido $record) => route('filament.admin.resources.pedidos.view', $record)),
            ])
            ->striped();
    }

    public function isReadOnly(): bool
    {
        return true;
    }
}