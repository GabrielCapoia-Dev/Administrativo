<?php

namespace App\Services;

use App\Models\TipoManutencao;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TipoManutencaoService
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

            Forms\Components\Textarea::make('descricao')
                ->label('Descrição')
                ->rows(3)
                ->maxLength(500),
        ];
    }

    public function configurarTabela(Table $table): Table
    {
        return $table
            ->query(TipoManutencao::query()->where('ativo', true))
            ->columns($this->colunasTabela())
            ->actions($this->acoesTabela())
            ->bulkActions($this->acoesEmMassa())
            ->defaultSort('nome', 'asc')
            ->striped();
    }

    protected function colunasTabela(): array
    {
        return [
            Tables\Columns\TextColumn::make('nome')
                ->label('Nome')
                ->searchable()
                ->sortable(),

            Tables\Columns\TextColumn::make('descricao')
                ->label('Descrição')
                ->limit(50)
                ->wrap(),

            Tables\Columns\TextColumn::make('pedidos_count')
                ->label('Pedidos')
                ->counts('pedidos')
                ->sortable(),

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
        return TipoManutencao::query()->where('ativo', true)->orderBy('nome');
    }
}