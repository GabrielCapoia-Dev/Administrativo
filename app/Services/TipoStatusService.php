<?php

namespace App\Services;

use App\Models\TipoStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TipoStatusService
{
    public function configurarFormulario(Form $form): Form
    {
        return $form->schema($this->schemaFormulario());
    }

    protected function schemaFormulario(): array
    {
        return [
            Forms\Components\TextInput::make('nome')
                ->label('Nome')
                ->required()
                ->maxLength(100),

            Forms\Components\ColorPicker::make('cor')
                ->label('Cor')
                ->required(),

            Forms\Components\TextInput::make('ordem')
                ->label('Ordem no fluxo')
                ->numeric()
                ->default(0)
                ->required(),

            Forms\Components\Toggle::make('finaliza_pedido')
                ->label('Finaliza o pedido?')
                ->helperText('Marca o pedido como concluído')
                ->inline(false)
                ->default(false),

            Forms\Components\Toggle::make('cancela_pedido')
                ->label('Cancela o pedido?')
                ->helperText('Marca o pedido como cancelado')
                ->inline(false)
                ->default(false),
        ];
    }

    public function configurarTabela(Table $table): Table
    {
        return $table
            ->query(TipoStatus::query()->where('ativo', true))
            ->columns($this->colunasTabela())
            ->actions($this->acoesTabela())
            ->bulkActions($this->acoesEmMassa())
            ->defaultSort('ordem', 'asc')
            ->striped();
    }

    protected function colunasTabela(): array
    {
        return [
            Tables\Columns\TextColumn::make('nome')
                ->label('Nome')
                ->searchable()
                ->sortable(),

            Tables\Columns\ColorColumn::make('cor')
                ->label('Cor'),

            Tables\Columns\TextColumn::make('ordem')
                ->label('Ordem')
                ->sortable(),

            Tables\Columns\IconColumn::make('finaliza_pedido')
                ->label('Finaliza')
                ->boolean(),

            Tables\Columns\IconColumn::make('cancela_pedido')
                ->label('Cancela')
                ->boolean(),

            Tables\Columns\TextColumn::make('updated_at')
                ->label('Atualizado em')
                ->since()
                ->sortable(),
        ];
    }

    protected function acoesTabela(): array
    {
        return [
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ];
    }

    protected function acoesEmMassa(): array
    {
        return [
            Tables\Actions\DeleteBulkAction::make(),
        ];
    }

    public function listarAtivos(): Builder
    {
        return TipoStatus::query()->where('ativo', true)->orderBy('ordem');
    }

    public function obterStatusInicial(): ?TipoStatus
    {
        return TipoStatus::where('ativo', true)
            ->orderBy('ordem')
            ->first();
    }
}