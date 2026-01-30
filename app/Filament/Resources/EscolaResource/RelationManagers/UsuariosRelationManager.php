<?php

namespace App\Filament\Resources\EscolaResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class UsuariosRelationManager extends RelationManager
{
    protected static string $relationship = 'usuarios';
    protected static ?string $title = 'Usuários Vinculados';
    protected static ?string $modelLabel = 'Usuário';
    protected static ?string $pluralModelLabel = 'Usuários';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Nível de Acesso')
                    ->badge(),

                Tables\Columns\IconColumn::make('email_approved')
                    ->label('Aprovado')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Desde')
                    ->since()
                    ->sortable(),
            ])
            ->defaultSort('name', 'asc')
            ->striped();
    }

    public function isReadOnly(): bool
    {
        return true;
    }
}