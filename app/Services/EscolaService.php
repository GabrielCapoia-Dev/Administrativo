<?php

namespace App\Services;

use App\Models\Escola;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class EscolaService
{
    public function ehAdmin(?User $user = null): bool
    {
        return Gate::allows('admin-only', $user);
    }

    public function configurarFormulario(Form $form): Form
    {
        return $form->schema($this->schemaFormulario());
    }

    protected function schemaFormulario(): array
    {
        return [
            Forms\Components\Section::make('Dados da Escola')
                ->icon('heroicon-o-building-library')
                ->schema([
                    Forms\Components\TextInput::make('nome')
                        ->label('Nome da Escola')
                        ->required()
                        ->maxLength(255)
                        ->columnSpan(2),

                    Forms\Components\TextInput::make('telefone')
                        ->label('Telefone')
                        ->tel()
                        ->mask('(99) 99999-9999')
                        ->maxLength(20),

                    Forms\Components\TextInput::make('email')
                        ->label('E-mail')
                        ->email()
                        ->maxLength(255),
                ])
                ->columns(2),
        ];
    }

    public function configurarTabela(Table $table, ?User $user): Table
    {
        return $table
            ->query(Escola::query()->where('ativo', true))
            ->columns($this->colunasTabela())
            ->filters($this->filtrosTabela())
            ->actions($this->acoesTabela($user))
            ->bulkActions($this->acoesEmMassa($user))
            ->defaultSort('nome', 'asc')
            ->striped();
    }

    protected function colunasTabela(): array
    {
        return [
            Tables\Columns\TextColumn::make('nome')
                ->label('Nome')
                ->searchable()
                ->sortable()
                ->wrap(),

            Tables\Columns\TextColumn::make('telefone')
                ->label('Telefone')
                ->searchable()
                ->copyable(),

            Tables\Columns\TextColumn::make('email')
                ->label('E-mail')
                ->searchable()
                ->copyable(),

            Tables\Columns\TextColumn::make('usuarios_count')
                ->label('Usuários')
                ->counts('usuarios')
                ->sortable()
                ->badge()
                ->color('info'),

            Tables\Columns\TextColumn::make('pedidos_count')
                ->label('Pedidos')
                ->counts('pedidos')
                ->sortable()
                ->badge()
                ->color('warning'),

            Tables\Columns\TextColumn::make('created_at')
                ->label('Criado em')
                ->dateTime('d/m/Y H:i')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),

            Tables\Columns\TextColumn::make('updated_at')
                ->label('Atualizado em')
                ->since()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ];
    }

    protected function filtrosTabela(): array
    {
        return [
            Tables\Filters\Filter::make('com_pedidos')
                ->label('Com Pedidos Abertos')
                ->query(fn(Builder $query) => $query->whereHas('pedidos', fn($q) => $q->where('ativo', true))),
        ];
    }

    protected function acoesTabela(?User $user): array
    {
        return [
            Tables\Actions\ViewAction::make(),
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make()
                ->visible(fn() => $this->ehAdmin($user)),
        ];
    }

    protected function acoesEmMassa(?User $user): array
    {
        if (!$this->ehAdmin($user)) return [];

        return [
            Tables\Actions\DeleteBulkAction::make(),
        ];
    }

    public function listarEscolasQuery(Builder $query, ?User $user): Builder
    {
        $query->where('ativo', true);

        // Se não for admin e tiver escola vinculada, só vê a própria
        if (!$this->ehAdmin($user) && $user?->id_escola) {
            $query->where('id', $user->id_escola);
        }

        return $query;
    }
}